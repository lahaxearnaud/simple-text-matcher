<?php

namespace alahaxe\SimpleTextMatcher\Classifiers;

/**
 * Class ClassificationResult
 * @package alahaxe\SimpleTextMatcher\Classifiers
 */
class ClassificationResult implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $classifier;

    /**
     * @var string
     */
    protected $intent;

    /**
     * @var float
     */
    protected $score;

    /**
     * @var float
     */
    protected $duration;

    /**
     * ClassificationResult constructor.
     * @param string $classifier
     * @param string $intent
     * @param float $score
     * @param float $duration
     */
    public function __construct(string $classifier, string $intent, float $score, float $duration = 0)
    {
        $this->classifier = $classifier;
        $this->intent = $intent;
        $this->score = $score;
        $this->duration = $duration;
    }


    /**
     * @return string
     */
    public function getClassifier(): string
    {
        return $this->classifier;
    }

    /**
     * @return float
     */
    public function getScore(): float
    {
        return $this->score;
    }

    /**
     * @return string
     */
    public function getIntent(): string
    {
        return $this->intent;
    }

    /**
     * @param float $duration
     */
    public function setDuration(float $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return float
     */
    public function getDuration(): float
    {
        return $this->duration;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'classifier' => $this->classifier,
            'score' => $this->score,
            'intent' => $this->intent,
            'duration' => $this->duration,
        ];
    }
}
