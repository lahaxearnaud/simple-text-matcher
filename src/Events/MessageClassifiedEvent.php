<?php

namespace alahaxe\SimpleTextMatcher\Events;

use alahaxe\SimpleTextMatcher\Message;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event is triggered when a message is classified
 *
 * @package alahaxe\SimpleTextMatcher\Events
 */
class MessageClassifiedEvent extends Event
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
