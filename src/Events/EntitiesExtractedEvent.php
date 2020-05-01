<?php

namespace Alahaxe\SimpleTextMatcher\Events;

use Alahaxe\SimpleTextMatcher\Message;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event is triggered when all data extractor are executed and the result is set on the message
 *
 * @package Alahaxe\SimpleTextMatcher\Events
 */
class EntitiesExtractedEvent extends Event implements MessageEvent
{
    /**
     * @var Message
     */
    protected $message;

    /**
     * MessageReceivedEvent constructor.
     *
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }
}
