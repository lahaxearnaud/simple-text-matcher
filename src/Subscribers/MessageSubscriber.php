<?php

namespace Alahaxe\SimpleTextMatcher\Subscribers;

use Alahaxe\SimpleTextMatcher\Events\MessageReceivedEvent;
use Alahaxe\SimpleTextMatcher\NegationDetector;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MessageSubscriber
 * @package Alahaxe\SimpleTextMatcher\Subscribers
 */
class MessageSubscriber implements EventSubscriberInterface
{

    /**
     * @var NegationDetector
     */
    protected $negationDetector;

    /**
     * MessageSubscriber constructor.
     * @param NegationDetector|null $negationDetector
     */
    public function __construct(NegationDetector $negationDetector = null)
    {
        $this->negationDetector = new NegationDetector();
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            MessageReceivedEvent::class => [
                'onMessageReceived'
            ]
        ];
    }

    /**
     * @param MessageReceivedEvent $event
     *
     * @return void
     */
    public function onMessageReceived(MessageReceivedEvent $event): void
    {
        $message = $event->getMessage();
        $message->setContainsNegation($this->negationDetector->isSentenceWithNegation($message));
    }
}
