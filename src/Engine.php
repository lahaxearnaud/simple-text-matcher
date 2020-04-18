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
     * @var array
     */
    protected $model;

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
     * @var string
     */
    protected $modelCachePath;

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
     * @param EventDispatcherInterface $eventDispatcher
     * @param ModelBuilder $modelBuilder
     * @param NormalizersBag $normalizers
     * @param ClassifiersBag $classifiers
     * @param Stemmer $stemmer
     * @param string $modelCachePath
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ModelBuilder $modelBuilder,
        NormalizersBag $normalizers,
        ClassifiersBag $classifiers,
        Stemmer $stemmer,
        string $modelCachePath
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->modelBuilder = $modelBuilder;
        $this->normalizers = $normalizers;
        $this->classifiers = $classifiers;
        $this->modelCachePath = $modelCachePath;
        $this->stemmer = $stemmer;

        $this->eventDispatcher->dispatch(new EngineBuildedEvent($this));
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->persistModels();
    }

    /**
     *
     */
    public function persistModels():void
    {
        if (file_exists($this->modelCachePath)) {
            return;
        }

        $cache = [];
        foreach ($this->classifiers->classifiersWithTraining() as $classifier) {
            $cache[get_class($classifier)] = $classifier->exportModel();
        }

        file_put_contents($this->modelCachePath, json_encode($cache, JSON_PRETTY_PRINT));
    }

    /**
     * @param array $training
     * @param array $synonyms
     *
     * @return Engine
     */
    public function prepare(array $training, array $synonyms):Engine
    {
        $this->model = $this->modelBuilder->build($training, $synonyms);

        $this->eventDispatcher->dispatch(new ModelExpandedEvent($this->model));

        $cachedModel = [];
        if (file_exists($this->modelCachePath)) {
            $cachedModel = json_decode(file_get_contents($this->modelCachePath), true);
        }

        foreach ($this->classifiers->classifiersWithTraining() as $classifier) {
            $classifier->setStemmer($this->stemmer);

            if (isset($cachedModel[get_class($classifier)])) {
                $classifier->reloadModel($cachedModel[get_class($classifier)]);
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
        $question->setClassification($bag->getTopIntents(3,0.3));

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
}
