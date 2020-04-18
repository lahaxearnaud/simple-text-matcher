<?php

namespace alahaxe\SimpleTextMatcher;

use alahaxe\SimpleTextMatcher\Classifiers\ClassificationResultsBag;
use alahaxe\SimpleTextMatcher\Classifiers\ClassifiersBag;
use alahaxe\SimpleTextMatcher\Events\EngineBuildedEvent;
use alahaxe\SimpleTextMatcher\Events\EngineStartedEvent;
use alahaxe\SimpleTextMatcher\Events\MessageClassifiedEvent;
use alahaxe\SimpleTextMatcher\Events\MessageCorrectedEvent;
use alahaxe\SimpleTextMatcher\Events\MessageReceivedEvent;
use alahaxe\SimpleTextMatcher\Events\ModelExpandedEvent;
use alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class Engine
 *
 * @package alahaxe\SimpleTextMatcher
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
     * Engine constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param ModelBuilder             $modelBuilder
     * @param NormalizersBag           $normalizers
     * @param ClassifiersBag           $classifiers
     * @param Stemmer                  $stemmer
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ModelBuilder $modelBuilder,
        NormalizersBag $normalizers,
        ClassifiersBag $classifiers,
        Stemmer $stemmer
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->modelBuilder = $modelBuilder;
        $this->normalizers = $normalizers;
        $this->classifiers = $classifiers;
        $this->stemmer = $stemmer;

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
            $this->model = $this->modelBuilder->build($training, $synonyms);
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

        $rawMessage = $question->getRawMessage();
        foreach ($this->normalizers->getOrderedByPriority() as $normalizer) {
            $rawMessage = $normalizer->normalize($rawMessage);
        }
        $question->setNormalizedMessage($rawMessage);

        $this->eventDispatcher->dispatch(new MessageCorrectedEvent($question));

        $bag = new ClassificationResultsBag();
        foreach ($this->classifiers->all() as $classifier) {
            $bag->merge($classifier->classify($question->getNormalizedMessage()));

            if ($bag->getResultsWithMinimumScore(1)->count() > 0) {
                break;
            }
        }
        $question->setClassification($bag->getTopIntents(3, 0.3));

        $bestResult = $question->getClassification()->offsetGet(0);
        if ($bestResult !== null) {
            $question->setIntentDetected($bestResult->getIntent());
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
}
