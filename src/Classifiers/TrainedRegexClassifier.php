<?php

namespace alahaxe\SimpleTextMatcher\Classifiers;

use alahaxe\SimpleTextMatcher\Stemmer;
use s9e\RegexpBuilder\Builder;

/**
 * Automatically generated regex based on training sentences
 *
 * Class TrainnedRegexClassifier
 *
 * @package alahaxe\SimpleTextMatcher\Classifiers
 */
class TrainedRegexClassifier implements TrainingInterface
{
    /**
     * @var [][]
     */
    protected $regexes = [];

    /**
     * @var Stemmer
     */
    protected $stemmer;

    /**
     * TrainedRegexClassifier constructor.
     * @param Stemmer|null $stemmer
     */
    public function __construct(Stemmer $stemmer = null)
    {
        $this->stemmer = $stemmer;
    }

    /**
     * @param string $question
     * @return ClassificationResultsBag
     */
    public function classify(string $question): ClassificationResultsBag
    {
        $bag = new ClassificationResultsBag();
        $startTimer = microtime(true);

        $question = $this->stemmer->stemPhrase($question);

        foreach ($this->regexes as $intent => $regexes) {
            foreach ($regexes as $regex) {
                $result = preg_match('/' . $regex . '/i', $question);
                if (!$result) {
                    continue;
                }

                $bag->add(
                    new ClassificationResult(
                        __CLASS__,
                        $intent,
                        $result
                    )
                );

                if ($result) {
                    break;
                }
            }
        }

        $bag->setExecutionTime(microtime(true) - $startTimer);

        return $bag;
    }

    /**
     * @param array $trainingData
     */
    public function prepareModel(array $trainingData = []): void
    {
        $builder = new Builder([
            'input' => 'Utf8',
            'output' => 'PHP'
        ]);


        foreach ($trainingData as $intent => $phrases) {
            foreach ($phrases as $index => $phrase) {
                $trainingData[$intent][$index] = $this->stemmer->stemPhrase($phrase);
            }
        }

        foreach ($trainingData as $intent => $phrases) {
            $this->regexes[$intent] = [];
            foreach (array_chunk($phrases, 10) as $chunk) {
                $this->regexes[$intent][] = $builder->build($chunk);
            }
        }
    }

    /**
     * @return mixed
     */
    public function exportModel()
    {
        return $this->regexes;
    }

    /**
     * @param mixed $modelData
     */
    public function reloadModel($modelData): void
    {
        $this->regexes = $modelData;
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
