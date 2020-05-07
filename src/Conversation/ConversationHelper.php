<?php


namespace Alahaxe\SimpleTextMatcher\Conversation;

use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorInterface;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\CancelExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\NumberExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\PercentageExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\UrlExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Whitelist\YesNoExtractor;
use Alahaxe\SimpleTextMatcher\Message;

/**
 * Class ConversationHelper
 * @package Alahaxe\SimpleTextMatcher\Conversation
 */
class ConversationHelper
{
    /**
     * @var Message
     */
    protected $message;

    /**
     * @var bool
     */
    protected $entityExtracted = false;

    /**
     * ConversationHelper constructor.
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
        $conversation = $message->getConversation() ?? new Conversation($message->getIntentDetected());
        $this->message->setConversation($conversation);

        $this->cancelIfAsked();
    }

    /**
     *
     */
    public function cancelIfAsked()
    {
        $entities = (new CancelExtractor())->extract($this->message->getRawMessage());
        if ($entities->isEmpty()) {
            return;
        }

        $this->reset();

        $this->entityExtracted = true;
        $this->message->setResponses(['OK, on oublie']);
    }

    /**
     *
     */
    public function reset()
    {
        $this->message->setConversation(null);
        $this->message->setExpectAnswer(false);
        $this->message->setIntentDetected(null);
    }

    /**
     * @param string $entityName
     * @param string $entityQuestion
     *
     * @return bool
     */
    public function confirm(string $entityName, string $entityQuestion):bool
    {
        return $this->askEntity($entityName, $entityQuestion, new YesNoExtractor());
    }

    /**
     * @param string $entityName
     * @param string $entityQuestion
     * @param string $validationRegex
     * @param string $entityType
     *
     * @return bool
     */
    public function ask(string $entityName, string $entityQuestion, string $validationRegex, string $entityType = 'string'):bool
    {
        $entities = $this->message->getEntities()->getByName($entityName);
        if ($entities->count() > 0) {
            return true;
        }

        if ($this->message->getConversation() === null) {
            return false;
        }

        $this->message->getConversation()->setEntityExpected($entityName);

        if (!preg_match($validationRegex, $this->message->getRawMessage())) {
            $this->message->setExpectAnswer(true);
            $this->message->setResponses([$entityQuestion]);
            return false;
        }

        $this->message->getConversation()->setEntityExpected(null);

        $entity = new Entity($entityType, $this->message->getRawMessage(), $entityName);

        $this->message->getEntities()->add($entity);
        $this->entityExtracted = true;

        return true;
    }

    /**
     * @param string $entityName
     * @param string $entityQuestion
     *
     * @return bool
     */
    public function askInt(string $entityName, string $entityQuestion):bool
    {
        if (!$this->ask($entityName, $entityQuestion, '/^[0-9]+$/', 'integer')) {
            return false;
        }

        $entity = $this->message->getEntities()->getByName($entityName)->first();
        $entity->setValue(intval($entity->getValue()));
        $this->message->getEntities()->replace($entity);

        return true;
    }

    /**
     * @param string $entityName
     * @param string $entityQuestion
     *
     * @return bool
     */
    public function askNumber(string $entityName, string $entityQuestion):bool
    {
        return $this->askEntity($entityName, $entityQuestion, new NumberExtractor());
    }

    /**
     * @param string $entityName
     * @param string $entityQuestion
     *
     * @return bool
     */
    public function askUrl(string $entityName, string $entityQuestion):bool
    {
        return $this->askEntity($entityName, $entityQuestion, new UrlExtractor());
    }

    /**
     * @param string $entityName
     * @param string $entityQuestion
     *
     * @return bool
     */
    public function askPercentage(string $entityName, string $entityQuestion):bool
    {
        return $this->askEntity($entityName, $entityQuestion, new PercentageExtractor());
    }

    /**
     * @param string $entityName
     * @param string $entityQuestion
     * @param EntityExtractorInterface $extractor
     *
     * @return bool
     */
    public function askEntity(string $entityName, string $entityQuestion, EntityExtractorInterface $extractor):bool
    {
        $entities = $this->message->getEntities()->getByName($entityName);
        if ($entities->count() > 0) {
            return true;
        }
        if ($this->message->getConversation() === null) {
            return false;
        }

        $this->message->getConversation()->setEntityExpected($entityName);

        $entities = $extractor->extract($this->message->getRawMessage());
        if ($entities->isEmpty()) {
            $this->message->setExpectAnswer(true);
            $this->message->setResponses([$entityQuestion]);
            return false;
        }

        /** @var Entity $entity */
        $entity = $entities->first();
        $entity->setName($entityName);

        $this->message->getEntities()->add($entity);
        $this->entityExtracted = true;

        return true;
    }
}
