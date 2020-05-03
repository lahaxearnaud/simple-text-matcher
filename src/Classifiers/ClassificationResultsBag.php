<?php

namespace Alahaxe\SimpleTextMatcher\Classifiers;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ClassificationResultBag
 *
 * @package Alahaxe\SimpleTextMatcher\Classifiers
 *
 * @template-extends ArrayCollection<int, ClassificationResult>
 */
class ClassificationResultsBag extends ArrayCollection implements \JsonSerializable
{
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @inheritDoc
     * @return     ClassificationResult[]
     */
    public function all()
    {
        return $this->toArray();
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

        foreach ($result as $element) {
            parent::add($element);
        }

        return $this;
    }

    /**
     * @param ClassificationResultsBag $bag
     *
     * @return self
     */
    public function merge(ClassificationResultsBag $bag) :self
    {
        foreach ($bag->toArray() as $result) {
            $this->add($result);
        }

        return $this;
    }

    /**
     * @param float $score
     *
     * @return ClassificationResultsBag
     */
    public function getResultsWithMinimumScore(float $score = 0):self
    {
        $results = array_filter(
            $this->toArray(),
            static function (ClassificationResult $result) use ($score) {
                return $result->getScore() >= $score;
            }
        );

        usort(
            $results,
            static function (ClassificationResult $classificationA, ClassificationResult $classificationB) {
                return $classificationA->getScore() < $classificationB->getScore();
            }
        );

        return new ClassificationResultsBag($results);
    }

    /**
     * @param  int $nbIntents
     * @param  float $score
     *
     * @return ClassificationResultsBag
     */
    public function getTopIntents(int $nbIntents, float $score):self
    {
        $results = [];
        /** @var ClassificationResult $classificationResult */
        foreach ($this->getResultsWithMinimumScore($score)->toArray() as $classificationResult) {
            $key = $classificationResult->getIntent().'_'.$classificationResult->getClassifier();
            $results[$key] = $classificationResult;

            if (count($results) >= $nbIntents) {
                break;
            }
        }

        return new ClassificationResultsBag(array_values($results));
    }

    /**
     * @param  float $duration
     * @return $this
     */
    public function setExecutionTime(float $duration)
    {
        /** @var ClassificationResult $result */
        foreach ($this->toArray() as $result) {
            $result->setDuration($duration);
        }

        return $this;
    }
}
