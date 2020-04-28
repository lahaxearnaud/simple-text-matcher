<?php

namespace Alahaxe\SimpleTextMatcher\Subscribers;

use Alahaxe\SimpleTextMatcher\Events\MessageReceivedEvent;
use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\FlagDetectorBag;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MessageSubscriber
 * @package Alahaxe\SimpleTextMatcher\Subscribers
 */
class MessageSubscriber implements EventSubscriberInterface
{

    /**
     * @var FlagDetectorBag
     */
    protected $flagDetectorBag;

    /**
     * MessageSubscriber constructor.
     *
     * @param FlagDetectorBag $flagDetectorBag
     */
    public function __construct(FlagDetectorBag $flagDetectorBag)
    {
        $this->flagDetectorBag = $flagDetectorBag;
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
        $this->flagDetectorBag->apply($event->getMessage());
    }
}
