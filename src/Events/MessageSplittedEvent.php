<?php

namespace Alahaxe\SimpleTextMatcher\Events;

use Alahaxe\SimpleTextMatcher\Message;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event is triggered when a message is splitted and all sub messages are classified
 *
 * @package Alahaxe\SimpleTextMatcher\Events
 */
class MessageSplittedEvent extends Event implements MessageEvent
{
    /**
     * @var Message
     */
    protected $message;

    /**
     * MessageSplittedEvent constructor.
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
