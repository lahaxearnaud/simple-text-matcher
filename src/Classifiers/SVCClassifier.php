<?php

namespace Alahaxe\SimpleTextMatcher\Classifiers;

use Alahaxe\SimpleTextMatcher\Stemmer;
use Phpml\Classification\SVC;
use Phpml\Dataset\ArrayDataset;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Pipeline;
use Phpml\SupportVectorMachine\Kernel;
use Phpml\Tokenization\WordTokenizer;

/**
 * Class SVCClassifier
 *
 * @package Alahaxe\SimpleTextMatcher\Classifiers
 */
class SVCClassifier implements TrainingInterface
{

    /**
     * @var Stemmer
     */
    protected $stemmer;

    /**
     * @var Pipeline
     */
    protected $model;

    /**
     * @var bool
     */
    protected $hasTrainedIntents = false;

    /**
     * @param string $question
     * @return ClassificationResultsBag
     */
    public function classify(string $question): ClassificationResultsBag
    {
        $resultBag = new ClassificationResultsBag();
        if (!$this->hasTrainedIntents) {
            return $resultBag;
        }

        if (empty(trim($question))) {
            return $resultBag;
        }

        $startTimer = microtime(true);

        $question = [$this->stemmer->stemPhrase($question)];
        /** @var \Phpml\Transformer $transformer */
        foreach ($this->model->getTransformers() as $transformer) {
            $transformer->transform($question);
        }

        $result = $this->model->getEstimator()->predictProbability($question);
        if (count($result[0]) === 0) {
            return $resultBag;
        }

        foreach ($result[0] as $intent => $score) {
            if ($score < 0.2) {
                continue;
            }

            $resultBag->add(new ClassificationResult(__CLASS__, $intent, $score));
        }

        $resultBag->setExecutionTime(microtime(true) - $startTimer);

        return $resultBag;
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
        $flattenTrainingData = [];
        foreach ($trainingData as $intent => $phrases) {
            if (count($phrases) < 10) {
                continue;
            }

            foreach ($phrases as $phrase) {
                $flattenTrainingData[$this->stemmer->stemPhrase($phrase)] = $intent;
            }
        }

        if (empty($flattenTrainingData)) {
            return;
        }

        $this->hasTrainedIntents = true;
        $dataset = new ArrayDataset(array_keys($flattenTrainingData), array_values($flattenTrainingData));
        unset($flattenTrainingData);

        $this->model = new Pipeline([
            new TokenCountVectorizer(new WordTokenizer()),
            new TfIdfTransformer()
        ], new SVC(
            Kernel::LINEAR,
            1.0,              // $cost
            3,              // $degree
            null,           // $gamma
            0.0,             // $coef0
            0.001,         // $tolerance
            100,          // $cacheSize
            true,          // $shrinking
            true   // $probabilityEstimates, set to true
        ));

        $this->model->train($dataset->getSamples(), $dataset->getTargets());
    }

    /**
     * @return mixed
     */
    public function exportModel()
    {
        return [
            'model' => serialize($this->model),
            'hasTrainedIntents' => $this->hasTrainedIntents
        ];
    }

    /**
     * @param mixed $modelData
     */
    public function reloadModel($modelData): void
    {
        $this->model = unserialize($modelData['model']);
        $this->hasTrainedIntents = $modelData['hasTrainedIntents'];
    }
}
