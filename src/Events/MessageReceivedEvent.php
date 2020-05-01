<?php

namespace Alahaxe\SimpleTextMatcher\Events;

use Alahaxe\SimpleTextMatcher\Engine;
use Alahaxe\SimpleTextMatcher\Message;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event is triggered when a message is send to the engine, before all alteration/classification
 *
 * @package Alahaxe\SimpleTextMatcher\Events
 */
class MessageReceivedEvent extends Event implements EngineEvent, MessageEvent
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
     * MessageReceivedEvent constructor.
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
