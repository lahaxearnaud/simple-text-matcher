<?php

namespace Alahaxe\SimpleTextMatcher\Events;

use Alahaxe\SimpleTextMatcher\Engine;
use Alahaxe\SimpleTextMatcher\Message;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event is triggered when a message is received with a conversation token
 *
 * @package Alahaxe\SimpleTextMatcher\Events
 */
class ConversationMessageReceivedEvent extends Event implements MessageEvent, EngineEvent
{
    /**
     * @var Message
     */
    protected $message;

    /**
     * @var Engine
     */
    protected $engine;

    /**
     * ConversationMessageEvent constructor.
     * @param Message $message
     * @param Engine $engine
     */
    public function __construct(Message $message, Engine $engine)
    {
        $this->message = $message;
        $this->engine = $engine;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }

    /**
     * @return Engine
     */
    public function getEngine(): Engine
    {
        return $this->engine;
    }
}
