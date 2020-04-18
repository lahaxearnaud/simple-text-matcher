<?php

namespace alahaxe\SimpleTextMatcher\Classifiers;

use alahaxe\SimpleTextMatcher\Stemmer;

/**
 * Class TextCompareClassifier
 * @package alahaxe\SimpleTextMatcher\Classifiers
 */
abstract class AbstractTextCompareClassifier implements TrainingInterface
{
    /**
     * @var array
     */
    protected $model;

    /**
     * @var Stemmer
     */
    protected $stemmer;

    /**
     * AbstractTextCompareClassifier constructor.
     * @param Stemmer|null $stemmer
     */
    public function __construct(Stemmer $stemmer = null)
    {
        $this->stemmer = $stemmer;
    }

    /**
     * @var string
     */
    protected $maxTrainingSize = 200;

    /**
     * @param string $question
     * @return ClassificationResultsBag
     */
    public function classify(string $question): ClassificationResultsBag
    {
        $startTimer = microtime(true);

        $question = $this->stemmer->stemPhrase($question);

        $bag = new ClassificationResultsBag();
        foreach ($this->model as $intent => $phrases) {
            foreach ($phrases as $phrase) {
                $result = $this->executeComparison($question, $phrase);

                if ($result >= $this->getMinimumAcceptableScore()) {
                    $bag->add(new ClassificationResult(get_class($this), $intent, $result));
                }

                if ($result === 1.) {
                    break 2;
                }
            }
        }

        $bag->setExecutionTime(microtime(true) - $startTimer);

        return $bag;
    }

    /**
     * @param string $question
     * @param string $modelPhrase
     * @return float
     */
    protected abstract function executeComparison(string $question, string $modelPhrase):float;

    /**
     * @return float
     */
    protected function getMinimumAcceptableScore() {
        return 0.6;
    }

    /**
     * @param array $trainingData
     */
    public function prepareModel(array $trainingData = []): void
    {
        $nbPerIntent = ceil($this->maxTrainingSize / max(1, count($trainingData)));

        foreach ($trainingData as $intent => $phrases) {
            $phrases = array_slice($phrases, 0, $nbPerIntent);
            foreach ($phrases as $index => $phrase) {
                $phrases[$index] = $this->stemmer->stemPhrase($phrase);
            }

            $this->model[$intent] = $phrases;
        }
    }

    /**
     * @return mixed
     */
    public function exportModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $modelData
     */
    public function reloadModel($modelData): void
    {
        $this->model = $modelData;
    }

    /**
     * @param string $maxTrainingSize
     */
    public function setMaxTrainingSize(string $maxTrainingSize): void
    {
        $this->maxTrainingSize = $maxTrainingSize;
    }

    /**
     * @return Stemmer
     */
    public function getStemmer(): Stemmer
    {
        return $this->stemmer;
    }

    /**
     * @param Stemmer $stemmer
     *
     * @return ClassifierInterface
     */
    public function setStemmer(Stemmer $stemmer): ClassifierInterface
    {
        $this->stemmer = $stemmer;

        return $this;
    }
}
