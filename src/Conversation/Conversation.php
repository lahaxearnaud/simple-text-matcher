<?php

namespace Alahaxe\SimpleTextMatcher\Conversation;

use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;

/**
 * Class Conversation
 *
 * @package Alahaxe\SimpleTextMatcher\Conversation
 */
class Conversation implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $conversationId;

    /**
     * @var EntityBag
     */
    protected $entities;

    /**
     * @var string
     */
    protected $intent;

    /**
     * @var string
     */
    protected $entityExpected;

    /**
     * Conversation constructor.
     * @param string|null $intent
     * @param string|null $conversationId
     * @param EntityBag|null $entities
     * @param string|null $entityExpected
     */
    public function __construct(string $intent = null, string $conversationId = null, EntityBag $entities = null, string $entityExpected = null)
    {
        $this->conversationId = $conversationId ?? md5(uniqid('', true));
        $this->entities = $entities ?? new EntityBag();
        $this->intent = $intent;
        $this->entityExpected = $entityExpected;
    }

    /**
     * @param string $token
     * @return Conversation
     */
    public static function buildFromToken(string $token):Conversation
    {
        $data = json_decode(@gzuncompress(base64_decode($token)), true);

        if (empty($data)) {
            throw new \InvalidArgumentException('Invalid conversation token');
        }

        $data = array_merge([
            'entities' => [],
            'conversationId' => null,
            'entityExpected' => null
        ], $data);

        $entities = array_map(static function ($item) {
            return new Entity($item['type'], $item['value'], $item['name'] ?? null);
        }, $data['entities']);

        return new Conversation($data['intent'], $data['conversationId'], new EntityBag($entities));
    }

    /**
     * @return string
     */
    public function getConversationId(): string
    {
        return $this->conversationId;
    }

    /**
     * @return EntityBag
     */
    public function getEntities(): EntityBag
    {
        return $this->entities;
    }

    /**
     * @return string
     */
    public function getIntent(): string
    {
        return $this->intent;
    }

    /**
     * @return string
     */
    public function getToken():string
    {
        return base64_encode(gzcompress($this->__toString()));
    }

    /**
     * @param string|null $entityExpected
     * @return Conversation
     */
    public function setEntityExpected(?string $entityExpected): Conversation
    {
        $this->entityExpected = $entityExpected;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return json_encode($this);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'conversationId' => $this->conversationId,
            'entities' => $this->entities->toArray(),
            'intent' => $this->intent,
            'entityExpected' => $this->entityExpected,
        ];
    }
}
