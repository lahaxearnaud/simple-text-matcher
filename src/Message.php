<?php


namespace alahaxe\SimpleTextMatcher;

use alahaxe\SimpleTextMatcher\Classifiers\ClassificationResultsBag;

/**
 * Class Message
 *
 * @package alahaxe\SimpleTextMatcher
 */
class Message implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $messageId;

    /**
     * @var string
     */
    protected $rawMessage;

    /**
     * @var string
     */
    protected $normalizedMessage;

    /**
     * @var string
     */
    protected $intentDetected;

    /**
     * @var ClassificationResultsBag
     */
    protected $classification;

    /**
     * @var int
     */
    private $receivedTimestamp = 0;

    /**
     * @var int
     */
    private $correctedTimestamp = 0;

    /**
     * @var int
     */
    private $classifiedTimestamp = 0;

    /**
     * Message constructor.
     *
     * @param string $rawMessage
     */
    public function __construct(string $rawMessage)
    {
        $this->rawMessage = $rawMessage;
        $this->messageId = uniqid(date('YmdHisu').'_', true);
        $this->receivedTimestamp = microtime(true);
    }

    /**
     * @return string
     */
    public function getRawMessage(): string
    {
        return $this->rawMessage;
    }

    /**
     * @return string
     */
    public function getNormalizedMessage(): string
    {
        return $this->normalizedMessage;
    }

    /**
     * @param string $normalizedMessage
     */
    public function setNormalizedMessage(string $normalizedMessage): void
    {
        $this->normalizedMessage = $normalizedMessage;
        $this->correctedTimestamp = microtime(true);
    }

    /**
     * @return string|null
     */
    public function getIntentDetected(): ?string
    {
        return $this->intentDetected;
    }

    /**
     * @param string $intentDetected
     */
    public function setIntentDetected(string $intentDetected): void
    {
        $this->intentDetected = $intentDetected;
    }

    /**
     * @return ClassificationResultsBag
     */
    public function getClassification(): ClassificationResultsBag
    {
        return $this->classification;
    }

    /**
     * @param ClassificationResultsBag $classification
     */
    public function setClassification(ClassificationResultsBag $classification): void
    {
        $this->classification = $classification;
        $this->classifiedTimestamp = microtime(true);
    }

    /**
     * @return string
     */
    public function getMessageId(): string
    {
        return $this->messageId;
    }

    /**
     * @return int[]
     *
     * @psalm-return array{receivedAt: int, correctedAt: int, classifiedAt: int, correctionDuration: int, classificationDuration: int}
     */
    public function getPerformance():array
    {
        return [
            'receivedAt' => $this->receivedTimestamp,
            'correctedAt' => $this->correctedTimestamp,
            'classifiedAt' => $this->classifiedTimestamp,
            'correctionDuration' => $this->correctedTimestamp - $this->receivedTimestamp,
            'classificationDuration' => $this->classifiedTimestamp - $this->correctedTimestamp,
        ];
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'messageId' => $this->messageId,
            'rawMessage' => $this->rawMessage,
            'normalizedMessage' => $this->normalizedMessage,
            'classification' => $this->classification,
            'intentDetected' => $this->intentDetected,
            'performance' => $this->getPerformance()
        ];
    }
}
