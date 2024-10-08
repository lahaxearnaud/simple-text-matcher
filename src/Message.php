<?php


namespace Alahaxe\SimpleTextMatcher;

use Alahaxe\SimpleTextMatcher\Classifiers\ClassificationResultsBag;
use Alahaxe\SimpleTextMatcher\Conversation\Conversation;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use Alahaxe\SimpleTextMatcher\MessageFlags\Flag;
use Alahaxe\SimpleTextMatcher\MessageFlags\FlagBag;

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
     * @var null|string
     */
    protected $parentMessageId;

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
     * @var Message[]
     */
    protected $subMessages = [];

    /**
     * @var FlagBag
     */
    protected $flags;

    /**
     * @var mixed[]
     */
    protected $responses = [];

    /**
     * @var array
     */
    protected $performance = [];

    /**
     * @var bool
     */
    protected $expectAnswer = false;

    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * Message constructor.
     *
     * @param string $rawMessage
     */
    public function __construct(string $rawMessage)
    {
        $this->rawMessage = $rawMessage;
        $this->normalizedMessage = $rawMessage;
        $this->messageId = uniqid(date('YmdHisu').'_', true);
        $this->entities = new EntityBag();
        $this->classification = new ClassificationResultsBag();
        $this->words = StringUtils::words($rawMessage);
        $this->nbWords = count($this->words);
        $this->flags = new FlagBag();
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
    public function getNormalizedMessage(): ?string
    {
        return $this->normalizedMessage;
    }

    /**
     * @param string $normalizedMessage
     */
    public function setNormalizedMessage(string $normalizedMessage): void
    {
        $this->normalizedMessage = $normalizedMessage;
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
    public function setIntentDetected(?string $intentDetected): void
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
     * @param Message[] $messages
     *
     * @return $this
     */
    public function addSubMessages(array $messages):self
    {
        foreach ($messages as $message) {
            $message->setParentMessageId($this->messageId);
        }

        $this->subMessages += $messages;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSubMessages():bool
    {
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
     * @param string $flag
     *
     * @return bool
     */
    public function hasFlag(string $flag): bool
    {
        return $this->flags->hasFlag($flag);
    }

    /**
     * @param Flag $flag
     * @return Message
     */
    public function addFlag(Flag $flag): Message
    {
        $this->flags->add($flag);

        return $this;
    }

    /**
     * @return FlagBag
     */
    public function getFlags(): FlagBag
    {
        return $this->flags;
    }

    /**
     * @return string|null
     */
    public function getParentMessageId(): ?string
    {
        return $this->parentMessageId;
    }

    /**
     * @param string|null $parentMessageId
     * @return Message
     */
    public function setParentMessageId(?string $parentMessageId): Message
    {
        $this->parentMessageId = $parentMessageId;
        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getResponses(): array
    {
        if (!$this->hasSubMessages()) {
            return $this->responses;
        }

        $responses = [];
        foreach ($this->getSubMessages() as $subMessage) {
            $responses = array_merge($responses, $subMessage->getResponses());
        }

        return $responses;
    }

    /**
     * @param mixed[] $responses
     * @return Message
     */
    public function setResponses(array $responses): Message
    {
        $this->responses = $responses;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasResponses():bool
    {
        if ($this->hasSubMessages()) {
            foreach ($this->getSubMessages() as $message) {
                if ($message->hasResponses()) {
                    return true;
                }
            }
        }

        return count($this->responses) > 0;
    }

    /**
     * @return array
     */
    public function getPerformance(): array
    {
        return $this->performance;
    }

    /**
     * @param array $performance
     * @return Message
     */
    public function setPerformance(array $performance): Message
    {
        $this->performance = $performance;

        return $this;
    }

    /**
     * @return bool
     */
    public function isExpectAnswer(): bool
    {
        return $this->expectAnswer;
    }

    /**
     * @param bool $expectAnswer
     * @return Message
     */
    public function setExpectAnswer(bool $expectAnswer): Message
    {
        $this->expectAnswer = $expectAnswer;
        return $this;
    }

    /**
     * @return Conversation|null
     */
    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    /**
     * @param Conversation|null $conversation
     * @return Message
     */
    public function setConversation(?Conversation $conversation): Message
    {
        $this->conversation = $conversation;
        return $this;
    }


    /**
     * @param string|null $conversationToken
     * @return $this
     */
    public function setConversationToken(?string $conversationToken): self
    {
        if ($conversationToken === null) {
            return $this;
        }

        $this->conversation = Conversation::buildFromToken($conversationToken);

        return $this;
    }
    /**
     * @return string
     */
    public function getConversationToken(): ?string
    {
        if ($this->conversation === null) {
            return null;
        }

        return $this->conversation->getToken();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'messageId' => $this->messageId,
            'parentMessageId' => $this->parentMessageId,
            'rawMessage' => $this->rawMessage,
            'normalizedMessage' => $this->normalizedMessage,
            'classification' => $this->classification,
            'entities' => $this->entities,
            'intentDetected' => $this->intentDetected,
            'nbWords' => $this->nbWords,
            'flags' => $this->flags,
            'responses' => $this->responses,
            'expectAnswer' => $this->expectAnswer,
            'conversationToken' => $this->getConversationToken(),
            'performance' => $this->getPerformance()
        ];
    }
}
