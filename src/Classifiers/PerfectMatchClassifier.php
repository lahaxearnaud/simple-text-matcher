<?php

namespace Alahaxe\SimpleTextMatcher\Classifiers;

use Alahaxe\SimpleTextMatcher\Stemmer;

/**
 * Class PerfectMatchClassifier
 * @package Alahaxe\SimpleTextMatcher\Classifiers
 */
class PerfectMatchClassifier implements TrainingInterface
{
    /**
     * @var array
     */
    protected $model = [];

    /**
     * @var Stemmer
     */
    protected $stemmer;

    /**
     * @param string $question
     * @return ClassificationResultsBag
     */
    public function classify(string $question): ClassificationResultsBag
    {
        $bag = new ClassificationResultsBag();
        $startTimer = microtime(true);
        $question = $this->stemmer->stemPhrase($question);

        if (isset($this->model[$question])) {
            $bag->add(new ClassificationResult(__CLASS__, $this->model[$question], 1));
        }

        $bag->setExecutionTime(microtime(true) - $startTimer);

        return $bag;
    }

    /**
     * @param Stemmer $stemmer
     * @return ClassifierInterface
     */
    public function setStemmer(Stemmer $stemmer): ClassifierInterface
    {
        $this->stemmer = $stemmer;

        return $this;
    }

    /**
     * @param array $trainingData
     */
    public function prepareModel(array $trainingData = []): void
    {
        foreach ($trainingData as $intent => $phrases) {
            foreach ($phrases as $phrase) {
                $this->model[$this->stemmer->stemPhrase($phrase)] = $intent;
            }
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
}
