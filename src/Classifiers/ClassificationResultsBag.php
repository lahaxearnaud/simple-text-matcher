<?php

namespace Alahaxe\SimpleTextMatcher\Classifiers;

/**
 * Class ClassificationResultBag
 *
 * @package Alahaxe\SimpleTextMatcher\Classifiers
 */
class ClassificationResultsBag implements \Countable, \JsonSerializable, \ArrayAccess
{

    /**
     * @var ClassificationResult[]
     */
    protected $results = [];

    /**
     * @inheritDoc
     */
    public function count()
    {
        return \count($this->results);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->results;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->results[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->results[$offset] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->results[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        if (isset($this->results[$offset])) {
            unset($this->results[$offset]);
        }
    }

    /**
     * @inheritDoc
     * @return     ClassificationResult[]
     */
    public function all()
    {
        return array_values($this->results);
    }

    /**
     * @param ClassificationResult|ClassificationResult[] $result
     *
     * @return self
     */
    public function add($result) :self
    {
        if (!is_array($result)) {
            $result = [$result];
        }
        $this->results = array_merge($this->results, $result);

        return $this;
    }

    /**
     * @param ClassificationResultsBag $bag
     *
     * @return self
     */
    public function merge(ClassificationResultsBag $bag) :self
    {
        $this->results = array_merge(
            $this->results,
            $bag->all()
        );

        return $this;
    }

    /**
     * @param float $score
     *
     * @return ClassificationResultsBag
     */
    public function getResultsWithMinimumScore(float $score = 0):ClassificationResultsBag
    {
        $results = array_filter(
            $this->results,
            static function (ClassificationResult $result) use ($score) {
                return $result->getScore() >= $score;
            }
        );

        usort(
            $results,
            static function (ClassificationResult $a, ClassificationResult $b) {
                return $a->getScore() < $b->getScore();
            }
        );

        $bag = new ClassificationResultsBag();
        $bag->add($results);

        return $bag;
    }

    /**
     * @param  int $nb
     * @param  int $score
     * @return ClassificationResultsBag
     */
    public function getTopIntents(int $nb, float $score):ClassificationResultsBag
    {
        $results = [];
        foreach ($this->getResultsWithMinimumScore($score)->all() as $classificationResult) {
            $key = $classificationResult->getIntent().'_'.$classificationResult->getClassifier();
            if (isset($results[$key])) {
                continue;
            }

            $results[$key] = $classificationResult;

            if (count($results) >= $nb) {
                break;
            }
        }

        $bag = new ClassificationResultsBag();
        $bag->add(array_values($results));

        return $bag;
    }

    /**
     * @param  float $duration
     * @return $this
     */
    public function setExecutionTime(float $duration)
    {
        foreach ($this->results as $result) {
            $result->setDuration($duration);
        }

        return $this;
    }
}
