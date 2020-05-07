<?php

namespace Alahaxe\SimpleTextMatcher\Subscribers;

use Alahaxe\SimpleTextMatcher\Events\ConversationMessageReceivedEvent;
use Alahaxe\SimpleTextMatcher\Events\EngineBuildedEvent;
use Alahaxe\SimpleTextMatcher\Events\EngineStartedEvent;
use Alahaxe\SimpleTextMatcher\Events\EntitiesExtractedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageClassifiedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageCorrectedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageReceivedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class PerformanceSubscriber
 *
 * @package Alahaxe\SimpleTextMatcher\Subscribers
 */
class PerformanceSubscriber implements EventSubscriberInterface
{

    /**
     * @var array
     */
    protected $performanceCollector = [];

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            MessageReceivedEvent::class => [
                'onMessageReceived'
            ],
            MessageCorrectedEvent::class => [
                'onMessageCorrected'
            ],
            MessageClassifiedEvent::class => [
                'onMessageClassified'
            ],
            EntitiesExtractedEvent::class => [
                'onEntitiesExtracted'
            ],
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

        $this->performanceCollector[$message->getMessageId()] = [
            'time' => [
                'received' => microtime(true),
                'corrected' => 0,
                'classified' => 0,
                'entitiesExtracted' => 0,
            ],
            'duration' => [
                'correction' => 0,
                'classification' => 0,
                'entitiesExtraction' => 0,
            ],
            'memory' => [
                'received' => memory_get_usage(),
                'corrected' => 0,
                'classified' => 0,
                'entitiesExtracted' => 0,
            ]
        ];
    }

    /**
     * @param MessageCorrectedEvent $event
     *
     * @return void
     */
    public function onMessageCorrected(MessageCorrectedEvent $event): void
    {
        $message = $event->getMessage();
        $this->performanceCollector[$message->getMessageId()]['time']['corrected'] = microtime(true);
        $this->performanceCollector[$message->getMessageId()]['memory']['corrected'] = memory_get_usage();
    }

    /**
     * @param MessageClassifiedEvent $event
     *
     * @return void
     */
    public function onMessageClassified(MessageClassifiedEvent $event): void
    {
        $message = $event->getMessage();
        $this->performanceCollector[$message->getMessageId()]['time']['classified'] = microtime(true);
        $this->performanceCollector[$message->getMessageId()]['memory']['classified'] = memory_get_usage();
    }
    /**
     * @param EntitiesExtractedEvent $event
     *
     * @return void
     */
    public function onEntitiesExtracted(EntitiesExtractedEvent $event): void
    {
        $message = $event->getMessage();
        $this->performanceCollector[$message->getMessageId()]['time']['entitiesExtracted'] = microtime(true);
        $this->performanceCollector[$message->getMessageId()]['memory']['entitiesExtracted'] = memory_get_usage();

        $times = $this->performanceCollector[$message->getMessageId()]['time'];

        $this->performanceCollector[$message->getMessageId()]['duration'] = [
            'correction' => $times['corrected'] - $times['received'],
            'classification' => $times['classified'] - $times['corrected'],
            'entitiesExtraction' => $times['entitiesExtracted'] - $times['classified'],
        ];

        $message->setPerformance($this->performanceCollector[$message->getMessageId()]);
    }
}
