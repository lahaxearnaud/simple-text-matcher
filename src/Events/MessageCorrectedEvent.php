<?php

namespace Alahaxe\SimpleTextMatcher\Events;

use Alahaxe\SimpleTextMatcher\Engine;
use Alahaxe\SimpleTextMatcher\Message;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event is triggered when a message is normalized, after all normalizers are executed
 *
 * @package Alahaxe\SimpleTextMatcher\Events
 */
class MessageCorrectedEvent extends Event implements EngineEvent, MessageEvent
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
     * MessageCorrectedEvent constructor.
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
