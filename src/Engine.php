<?php

namespace Alahaxe\SimpleTextMatcher;

use Alahaxe\SimpleTextMatcher\Classifiers\ClassificationResultsBag;
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
     *
     * @return self
     */
    public function prepare(array $training, array $synonyms):self
    {
        $modelUpToDate = !empty($this->classifierTrainedModels) && !empty($this->model);

        foreach ($this->classifiers->classifiersWithTraining() as $classifier) {
            $modelUpToDate = $modelUpToDate && isset($this->classifierTrainedModels[get_class($classifier)]);
        }

        if (!$modelUpToDate) {
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
     * @param string|Message $question
     *
     * @return Message
     */
    public function predict($question) :Message
    {
        if (!$question instanceof Message) {
            $question = new Message($question);
        }

        $this->eventDispatcher->dispatch(new MessageReceivedEvent($question));

        $question->setNormalizedMessage($this->normalizers->apply($question->getRawMessage()));

        $this->eventDispatcher->dispatch(new MessageCorrectedEvent($question));

        $bag = $this->executeClassifiers($question);

        $question->setClassification($bag->getTopIntents(3, 0.3));

        $bestResult = $question->getClassification()->offsetGet(0);
        if ($bestResult !== null) {
            $question->setIntentDetected($bestResult->getIntent());
            $question->setEntities($this->extractors->apply($question->getRawMessage()));
            $this->eventDispatcher->dispatch(new EntitiesExtractedEvent($question));
        }

        $this->eventDispatcher->dispatch(new MessageClassifiedEvent($question));

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
}
