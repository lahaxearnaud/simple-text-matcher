<?php

namespace Alahaxe\SimpleTextMatcher;

use Alahaxe\SimpleTextMatcher\Classifiers\ClassifierInterface;
use Alahaxe\SimpleTextMatcher\Classifiers\ClassifiersBag;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorsBag;
use Alahaxe\SimpleTextMatcher\Events\BeforeModelBuildEvent;
use Alahaxe\SimpleTextMatcher\Events\ConversationMessageReceivedEvent;
use Alahaxe\SimpleTextMatcher\Events\EngineBuildedEvent;
use Alahaxe\SimpleTextMatcher\Events\EngineStartedEvent;
use Alahaxe\SimpleTextMatcher\Events\EntitiesExtractedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageClassifiedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageCorrectedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageReceivedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageRespondedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageSplittedEvent;
use Alahaxe\SimpleTextMatcher\Events\ModelExpandedEvent;
use Alahaxe\SimpleTextMatcher\Handlers\AbstractHandler;
use Alahaxe\SimpleTextMatcher\Loader\LoaderInterface;
use Alahaxe\SimpleTextMatcher\Normalizers\NormalizerInterface;
use Alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Engine
 *
 * @package Alahaxe\SimpleTextMatcher
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var EntityExtractorsBag
     */
    protected $extractors;

    /**
     * @var QuestionSplitter
     */
    protected $questionSplitter;

    /**
     * Engine constructor.
     * @param EventDispatcher $eventDispatcher
     * @param ModelBuilder $modelBuilder
     * @param NormalizersBag $normalizers
     * @param ClassifiersBag $classifiers
     * @param EntityExtractorsBag $extractors
     * @param Stemmer $stemmer
     * @param QuestionSplitter|null $questionSplitter
     */
    public function __construct(
        EventDispatcher $eventDispatcher,
        ModelBuilder $modelBuilder,
        NormalizersBag $normalizers,
        ClassifiersBag $classifiers,
        EntityExtractorsBag $extractors,
        Stemmer $stemmer,
        QuestionSplitter $questionSplitter = null
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->modelBuilder = $modelBuilder;
        $this->normalizers = $normalizers;
        $this->classifiers = $classifiers;
        $this->stemmer = $stemmer;
        $this->extractors = $extractors;
        $this->questionSplitter = $questionSplitter ?? new QuestionSplitter();

        $this->eventDispatcher->dispatch(new EngineBuildedEvent($this));
    }

    /**
     * @param LoaderInterface $loader
     *
     * @return $this
     */
    public function prepareWithLoader(LoaderInterface $loader):self
    {
        $model = $loader->load();
        foreach ($model->getIntentHandlers() as $intentHandler) {
            $this->getEventDispatcher()->addSubscriber($intentHandler);
        }

        return $this->prepare($model->getTraining(), $model->getSynonyms(), $model->getIntentExtractors());
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

        if ($this->needToRebuildModel($modelSignature)) {
            $this->modelSignature = $modelSignature;
            $this->prepareModel($training, $synonyms);
        }

        $this->prepareClassifiers();
        $this->eventDispatcher->dispatch(new EngineStartedEvent($this));

        return $this;
    }

    /**
     * @param Message $question
     * @return Message
     */
    protected function classifyMessage(Message $question):Message
    {
        // flag detection / normalisation is done on this event (see: MessageSubscriber)
        $this->eventDispatcher->dispatch(new MessageReceivedEvent($question, $this));

        if ($question->getConversationToken() === null) {
            // classification is done on this event (see: ClassificationSubscriber)
            $this->eventDispatcher->dispatch(new MessageCorrectedEvent($question, $this));

            // entity extraction is done on this event (see: EntitySubscriber)
            $this->eventDispatcher->dispatch(new MessageClassifiedEvent($question, $this));

            // final version of the message with intent, entities and flags
            $this->eventDispatcher->dispatch(new EntitiesExtractedEvent($question));
        } else {

            // bypass classification and load conversation context / invoke handler
            $this->eventDispatcher->dispatch(new ConversationMessageReceivedEvent($question, $this));
        }

        // after intent handler
        $this->eventDispatcher->dispatch(new MessageRespondedEvent($question));

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

        if (!$allowSplit || $question->getConversationToken() !== null) {
            return $this->classifyMessage($question);
        }

        $subQuestions = $this->questionSplitter->splitQuestion($question);
        $nbSubQuestions = count($subQuestions);

        // only one question detected fallback to legacy behavior
        if ($nbSubQuestions === 1) {
            return $this->classifyMessage($question);
        }

        $allSubQuestionClassified = true;
        /** @var Message $subQuestion */
        foreach ($subQuestions as $subQuestion) {
            $this->classifyMessage($subQuestion);
            if ($subQuestion->getIntentDetected() === null) {
                $allSubQuestionClassified = false;
            }
        }

        // if we had only one part we may try to classify the full sentence
        if (!$allSubQuestionClassified) {
            $this->classifyMessage($question);

            // this should me more meaningfull
            if ($question->getIntentDetected() !== null) {
                return $question;
            }
        }

        $question->addSubMessages($subQuestions);

        $this->eventDispatcher->dispatch(new MessageSplittedEvent($question));

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
     * @param array $training
     * @param array $synonyms
     */
    protected function prepareModel(array $training, array $synonyms):void
    {
        $this->eventDispatcher->dispatch(new BeforeModelBuildEvent($this));
        $this->modelBuilder->setNormalizers($this->normalizers);
        $this->model = $this->modelBuilder->build($training, $synonyms);
        $this->eventDispatcher->dispatch(new ModelExpandedEvent($this->model));
    }

    /**
     *
     */
    protected function prepareClassifiers():void
    {
        foreach ($this->classifiers->classifiersWithTraining() as $classifier) {
            $classifier->setStemmer($this->stemmer);
            $classifierClass = get_class($classifier);
            if (isset($this->classifierTrainedModels[$classifierClass])) {
                $classifier->reloadModel($this->classifierTrainedModels[$classifierClass]);
                continue;
            }

            $classifier->prepareModel($this->model);
        }
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
     * @return EventDispatcher
     */
    public function getEventDispatcher(): EventDispatcher
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
     * @return array
     */
    public function getIntentExtractors(): array
    {
        return $this->intentExtractors;
    }

    /**
     * @param int $currentSignature
     * @return bool
     */
    protected function needToRebuildModel(int $currentSignature):bool
    {
        return empty($this->classifierTrainedModels)
            || empty($this->model)
            || !isset($this->modelSignature)
            || $this->modelSignature !== $currentSignature // detect changes in data or configuration
        ;
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

    /**
     * @param AbstractHandler $abstractHandler
     *
     * @return $this
     */
    public function registerHandler(AbstractHandler $abstractHandler):self
    {
        $this->getEventDispatcher()->addSubscriber($abstractHandler);

        return $this;
    }
}
