<?php

namespace alahaxe\SimpleTextMatcher\Classifiers;

use alahaxe\SimpleTextMatcher\Stemmer;
use TextAnalysis\Classifiers\NaiveBayes;
use TextAnalysis\Tokenizers\GeneralTokenizer;

/**
 * Class BayesClassifier
 *
 * @package alahaxe\SimpleTextMatcher\Classifiers
 */
class NaiveBayesClassifier implements TrainingInterface
{

    /**
     * @var NaiveBayes
     */
    protected $model;

    /**
     * @var Stemmer|null
     */
    protected $stemmer;

    /**
     * TrainedRegexClassifier constructor.
     *
     * @param Stemmer $stemmer
     */
    public function __construct(Stemmer $stemmer = null)
    {
        $this->stemmer = $stemmer;
    }

    /**
     * @param  string $question
     * @return ClassificationResultsBag
     */
    public function classify(string $question): ClassificationResultsBag
    {
        $bag = new ClassificationResultsBag();
        $startTimer = microtime(true);

        if (empty($question)) {
            return $bag;
        }

        $result = $this->model->predict($this->stemTokens($this->tokenize($question)));

        foreach ($result as $intent => $score) {
            if ($score < 0.26) {
                continue;
            }

            $bag->add(new ClassificationResult(__CLASS__, $intent, $score));
        }

        $bag->setExecutionTime(microtime(true) - $startTimer);

        return $bag;
    }

    /**
     * @param  string $question
     * @return array
     */
    protected function tokenize(string $question)
    {

        return (new GeneralTokenizer())->tokenize($question);
    }

    /**
     * @param  array $tokens
     * @return array
     */
    protected function stemTokens(array $tokens)
    {
        foreach ($tokens as &$token) {
            $token = $this->stemmer->stem($token);
        }

        return $tokens;
    }

    /**
     * @param array $trainingData
     */
    public function prepareModel(array $trainingData = []): void
    {
        $this->model = new NaiveBayes();

        foreach ($trainingData as $intent => $phrases) {
            foreach ($phrases as $phrase) {
                $this->model->train($intent, $this->stemTokens($this->tokenize($phrase)));
            }
        }
    }

    /**
     * @return mixed
     */
    public function exportModel()
    {
        return serialize($this->model);
    }

    /**
     * @param mixed $modelData
     */
    public function reloadModel($modelData): void
    {
        $this->model = unserialize($modelData);
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
     * @return self
     */
    public function setStemmer(Stemmer $stemmer): ClassifierInterface
    {
        $this->stemmer = $stemmer;

        return $this;
    }
}
