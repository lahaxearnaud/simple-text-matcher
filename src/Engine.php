<?php

namespace Alahaxe\SimpleTextMatcher;

use Alahaxe\SimpleTextMatcher\Classifiers\ClassificationResultsBag;
use Alahaxe\SimpleTextMatcher\Classifiers\ClassifierInterface;
use Alahaxe\SimpleTextMatcher\Classifiers\ClassifiersBag;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorsBag;
use Alahaxe\SimpleTextMatcher\Events\BeforeModelBuildEvent;
use Alahaxe\SimpleTextMatcher\Events\EngineBuildedEvent;
use Alahaxe\SimpleTextMatcher\Events\EngineStartedEvent;
use Alahaxe\SimpleTextMatcher\Events\EntitiesExtractedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageClassifiedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageCorrectedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageReceivedEvent;
use Alahaxe\SimpleTextMatcher\Events\ModelExpandedEvent;
use Alahaxe\SimpleTextMatcher\Normalizers\NormalizerInterface;
use Alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class Engine
 *
 * @package Alahaxe\SimpleTextMatcher
 */
class Engine
{
    /**
     * Expanded training phases
     *
     * @var array
     */
    protected $model = [];

    /**
     * @var int
     */
    protected $modelSignature;

    /**
     * Models build by classifiers
     *
     * @var array
     */
    protected $classifierTrainedModels = [];

    /**
     * @var ModelBuilder
     */
    protected $modelBuilder;

    /**
     * @var array
     */
    protected $intentExtractors = [];

    /**
     * @var NormalizersBag
     */
    protected $normalizers;

    /**
     * @var ClassifiersBag
     */
    protected $classifiers;

    /**
     * @var Stemmer
     */
    protected $stemmer;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var EntityExtractorsBag
     */
    protected $extractors;

    /**
     * Engine constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param ModelBuilder $modelBuilder
     * @param NormalizersBag $normalizers
     * @param ClassifiersBag $classifiers
     * @param EntityExtractorsBag $extractors
     * @param Stemmer $stemmer
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ModelBuilder $modelBuilder,
        NormalizersBag $normalizers,
        ClassifiersBag $classifiers,
        EntityExtractorsBag $extractors,
        Stemmer $stemmer
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->modelBuilder = $modelBuilder;
        $this->normalizers = $normalizers;
        $this->classifiers = $classifiers;
        $this->stemmer = $stemmer;
        $this->extractors = $extractors;

        $this->eventDispatcher->dispatch(new EngineBuildedEvent($this));
    }

    /**
     * @param array $training
     * @param array $synonyms
     * @param array $intentExtractors
     *
     * @return $this
     */
    public function prepare(array $training, array $synonyms, array $intentExtractors = []):self
    {
        $this->intentExtractors = $intentExtractors;

        $modelSignature = $this->generateCacheSignature($training, $synonyms, $intentExtractors);

        $modelUpToDate = !empty($this->classifierTrainedModels)
            && !empty($this->model)
            && isset($this->modelSignature)
            && $this->modelSignature === $modelSignature // detect changes in data or configuration
        ;

        if (!$modelUpToDate) {
            $this->modelSignature = $modelSignature;
            $this->eventDispatcher->dispatch(new BeforeModelBuildEvent($this));
            $this->modelBuilder->setNormalizers($this->normalizers);
            $this->model = $this->modelBuilder->build($training, $synonyms);
            $this->eventDispatcher->dispatch(new EngineBuildedEvent($this));
        }

        $this->eventDispatcher->dispatch(new ModelExpandedEvent($this->model));

        foreach ($this->classifiers->classifiersWithTraining() as $classifier) {
            $classifier->setStemmer($this->stemmer);

            if (isset($this->classifierTrainedModels[get_class($classifier)])) {
                $classifier->reloadModel($this->classifierTrainedModels[get_class($classifier)]);
            } else {
                $classifier->prepareModel($this->model);
            }
        }

        $this->eventDispatcher->dispatch(new EngineStartedEvent($this));

        return $this;
    }

    /**
     * @param Message $question
     *
     * @return ClassificationResultsBag
     */
    protected function executeClassifiers(Message $question):ClassificationResultsBag
    {
        $bag = new ClassificationResultsBag();
        foreach ($this->classifiers->all() as $classifier) {
            $bag->merge($classifier->classify($question->getNormalizedMessage()));

            if ($bag->getResultsWithMinimumScore(1)->count() > 0) {
                break;
            }
        }

        return $bag;
    }


    /**
     * @param Message $question
     * @return Message
     */
    protected function classifyMessage(Message $question):Message
    {
        $this->eventDispatcher->dispatch(new MessageReceivedEvent($question));

        $question->setNormalizedMessage($this->normalizers->apply($question->getRawMessage()));

        $this->eventDispatcher->dispatch(new MessageCorrectedEvent($question));

        $bag = $this->executeClassifiers($question);
        $question->setClassification($bag->getTopIntents(3, 0.3));

        $bestResult = $question->getClassification()->offsetGet(0);
        if ($bestResult !== null) {
            $intent = $bestResult->getIntent();
            $question->setIntentDetected($intent);

            if (isset($this->intentExtractors[$intent])) {
                $question->setEntities(
                    $this->extractors->getByTypes($this->intentExtractors[$intent])->apply($question->getRawMessage())
                );
            }

            $this->eventDispatcher->dispatch(new EntitiesExtractedEvent($question));
        }

        $this->eventDispatcher->dispatch(new MessageClassifiedEvent($question));

        return $question;
    }

    /**
     * @param string|Message $question
     * @param bool $allowSplit (try to detect sub intent)
     *
     * @return Message
     */
    public function predict($question, $allowSplit = false):Message
    {
        if (!$question instanceof Message) {
            $question = new Message($question);
        }

        if (!$allowSplit) {
            return $this->classifyMessage($question);
        }

        $messageSplitter = new QuestionSplitter();
        $subQuestions = $messageSplitter->splitQuestion($question);
        $nbSubQuestions = count($subQuestions);

        // only one question detected fallback to legacy behavior
        if ($nbSubQuestions === 1) {
            return $this->classifyMessage($question);
        }

        /** @var Message $subQuestion */
        foreach ($subQuestions as $subQuestion) {
            $this->classifyMessage($subQuestion);
        }

        $nbIntentsDetected = count(array_filter($subQuestions, static function (Message $message) {
            return $message->getIntentDetected() !== null;
        }));

        // if we had only one part we may try to classify the full sentence
        if ($nbIntentsDetected < $nbSubQuestions) {
            $this->classifyMessage($question);

            // this should me more meaningfull
            if ($question->getIntentDetected() !== null) {
                return $question;
            }
        }

        $question->addSubMessages($subQuestions);

        return $question;
    }

    /**
     * @return Stemmer
     */
    public function getStemmer(): Stemmer
    {
        return $this->stemmer;
    }

    /**
     * @return array
     *
     * @psalm-return array<string, mixed>
     */
    public function exportTrainedModels(): array
    {
        $cache = [];
        foreach ($this->getClassifiers()->classifiersWithTraining() as $classifier) {
            $cache[get_class($classifier)] = $classifier->exportModel();
        }

        return $cache;
    }

    /**
     * @param array $classifierTrainedModels
     */
    public function setClassifierTrainedModels(array $classifierTrainedModels): void
    {
        $this->classifierTrainedModels = $classifierTrainedModels;
    }

    /**
     * @return ClassifiersBag
     */
    public function getClassifiers(): ClassifiersBag
    {
        return $this->classifiers;
    }

    /**
     * @return array
     */
    public function getModel(): array
    {
        return $this->model;
    }

    /**
     * @param array $model
     */
    public function setModel(array $model): void
    {
        $this->model = $model;
    }

    /**
     * @return ModelBuilder
     */
    public function getModelBuilder(): ModelBuilder
    {
        return $this->modelBuilder;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * @return NormalizersBag
     */
    public function getNormalizers(): NormalizersBag
    {
        return $this->normalizers;
    }

    /**
     * @return EntityExtractorsBag
     */
    public function getExtractors(): EntityExtractorsBag
    {
        return $this->extractors;
    }

    /**
     * @return int
     */
    public function getModelSignature(): int
    {
        return $this->modelSignature;
    }

    /**
     * @param int $modelSignature
     * @return Engine
     */
    public function setModelSignature(int $modelSignature): Engine
    {
        $this->modelSignature = $modelSignature;

        return $this;
    }

    /**
     * @param array $training
     * @param array $synonyms
     * @param array $intentExtractors
     *
     * @return int
     */
    protected function generateCacheSignature(array $training, array $synonyms, array $intentExtractors)
    {
        $cacheKey = serialize($training)
            .serialize($synonyms)
            .serialize($intentExtractors)
            .serialize(array_map(static function (NormalizerInterface $normalizer) {
                return get_class($normalizer);
            }, $this->normalizers->all()))
            .serialize(array_map(static function (ClassifierInterface $classifier) {
                return get_class($classifier);
            }, $this->classifiers->all()))
            .get_class($this->stemmer);
        ;

        // faster than hash
        return crc32($cacheKey);
    }
}
