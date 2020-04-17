<?php

namespace alahaxe\SimpleTextMatcher\Events;

use alahaxe\SimpleTextMatcher\Message;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class MessageReceivedEvent
 * @package alahaxe\SimpleTextMatcher\Events
 */
class MessageReceivedEvent extends Event
{
    /**
     * @var Message
     */
    protected $message;

    /**
     * MessageReceivedEvent constructor.
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
