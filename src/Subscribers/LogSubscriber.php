<?php

namespace alahaxe\SimpleTextMatcher\Subscribers;

use alahaxe\SimpleTextMatcher\Events\EngineBuildedEvent;
use alahaxe\SimpleTextMatcher\Events\EngineStartedEvent;
use alahaxe\SimpleTextMatcher\Events\MessageClassifiedEvent;
use alahaxe\SimpleTextMatcher\Events\MessageCorrectedEvent;
use alahaxe\SimpleTextMatcher\Events\MessageReceivedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogSubscriber
 * @package alahaxe\SimpleTextMatcher\Subscribers
 */
class LogSubscriber implements EventSubscriberInterface
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * LogSubscriber constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            EngineBuildedEvent::class => [
                'onEngineBuilded'
            ],
            EngineStartedEvent::class => [
                'onEngineStarted'
            ],
            MessageReceivedEvent::class => [
                'onMessageReceived'
            ],
            MessageCorrectedEvent::class => [
                'onMessageCorrected'
            ],
            MessageClassifiedEvent::class => [
                'onMessageClassified'
            ],
        ];
    }

    /**
     * @param EngineBuildedEvent $event
     */
    public function onEngineBuilded(EngineBuildedEvent $event)
    {
        $this->logger->debug('Engine builded');
    }

    /**
     * @param EngineStartedEvent $event
     */
    public function onEngineStarted(EngineStartedEvent $event)
    {
        $this->logger->debug('Engine started');
    }

    /**
     * @param MessageReceivedEvent $event
     */
    public function onMessageReceived(MessageReceivedEvent $event)
    {
        $this->logger->info('Message received', $event->getMessage()->jsonSerialize());
    }

    /**
     * @param MessageCorrectedEvent $event
     */
    public function onMessageCorrected(MessageCorrectedEvent $event)
    {
        $this->logger->info('Message corrected', $event->getMessage()->jsonSerialize());
    }

    /**
     * @param MessageClassifiedEvent $event
     */
    public function onMessageClassified(MessageClassifiedEvent $event)
    {
        $this->logger->info('Message classified', $event->getMessage()->jsonSerialize());
    }
}
