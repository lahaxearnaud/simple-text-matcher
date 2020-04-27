<?php

namespace Alahaxe\SimpleTextMatcher\Subscribers;

use Alahaxe\SimpleTextMatcher\Events\MessageReceivedEvent;
use Alahaxe\SimpleTextMatcher\MessageFlags\NegationFlagDetector;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MessageSubscriber
 * @package Alahaxe\SimpleTextMatcher\Subscribers
 */
class MessageSubscriber implements EventSubscriberInterface
{

    /**
     * @var NegationFlagDetector
     */
    protected $negationDetector;

    /**
     * MessageSubscriber constructor.
     *
     * @param NegationFlagDetector|null $negationDetector
     */
    public function __construct(NegationFlagDetector $negationDetector = null)
    {
        $this->negationDetector = $negationDetector ?? new NegationFlagDetector();
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
        if ($this->negationDetector->detect($message)) {
            $message->addFlag($this->negationDetector->getFlagName());
        }
    }
}
