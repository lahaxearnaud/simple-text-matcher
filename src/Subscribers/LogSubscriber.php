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
 *
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
     *
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
     *
     * @return void
     */
    public function onEngineBuilded(EngineBuildedEvent $event): void
    {
        $this->logger->debug('Engine builded');
    }

    /**
     * @param EngineStartedEvent $event
     *
     * @return void
     */
    public function onEngineStarted(EngineStartedEvent $event): void
    {
        $this->logger->debug('Engine started');
    }

    /**
     * @param MessageReceivedEvent $event
     *
     * @return void
     */
    public function onMessageReceived(MessageReceivedEvent $event): void
    {
        $this->logger->info('Message received', $event->getMessage()->jsonSerialize());
    }

    /**
     * @param MessageCorrectedEvent $event
     *
     * @return void
     */
    public function onMessageCorrected(MessageCorrectedEvent $event): void
    {
        $this->logger->info('Message corrected', $event->getMessage()->jsonSerialize());
    }

    /**
     * @param MessageClassifiedEvent $event
     *
     * @return void
     */
    public function onMessageClassified(MessageClassifiedEvent $event): void
    {
        $this->logger->info('Message classified', $event->getMessage()->jsonSerialize());
    }
}
