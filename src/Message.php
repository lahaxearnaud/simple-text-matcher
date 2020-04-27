<?php


namespace Alahaxe\SimpleTextMatcher;

use Alahaxe\SimpleTextMatcher\Classifiers\ClassificationResultsBag;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;

/**
 * Class Message
 *
 * @package Alahaxe\SimpleTextMatcher
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
     * @var EntityBag
     */
    protected $entities;

    /**
     * @var int
     */
    protected $nbWords;

    /**
     * @var string[]
     */
    protected $words = [];

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
     * @var Message[]
     */
    protected $subMessages = [];

    /**
     * @var bool
     */
    protected $containsNegation = false;

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
        $this->entities = new EntityBag();
        $this->classification = new ClassificationResultsBag();
        $this->words = explode(' ', $rawMessage);
        $this->nbWords = count($this->words);
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
     * @return EntityBag
     */
    public function getEntities(): EntityBag
    {
        return $this->entities;
    }

    /**
     * @param EntityBag $entities
     */
    public function setEntities(EntityBag $entities): void
    {
        $this->entities = $entities;
    }

    /**
     * @return int
     */
    public function getNbWords(): int
    {
        return $this->nbWords;
    }

    /**
     * @return string[]
     */
    public function getWords(): array
    {
        return $this->words;
    }

    /**
     * @param Message|Message[] $messages
     *
     * @return $this
     */
    public function addSubMessages($messages):self
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }

        $this->subMessages += $messages;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSubMessages():bool {
        return count($this->subMessages) > 0;
    }

    /**
     * @return Message[]
     */
    public function getSubMessages(): array
    {
        return $this->subMessages;
    }

    /**
     * @return bool
     */
    public function isContainsNegation(): bool
    {
        return $this->containsNegation;
    }

    /**
     * @param bool $containsNegation
     * @return Message
     */
    public function setContainsNegation(bool $containsNegation): Message
    {
        $this->containsNegation = $containsNegation;
        return $this;
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
            'entities' => $this->entities,
            'intentDetected' => $this->intentDetected,
            'nbWords' => $this->nbWords,
            'containsNegation' => $this->containsNegation,
            'performance' => $this->getPerformance()
        ];
    }
}
