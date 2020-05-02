<?php

namespace alahaxe\SimpleTextMatcher\Tests;

use Alahaxe\SimpleTextMatcher\Events\BeforeModelBuildEvent;
use Alahaxe\SimpleTextMatcher\Events\EngineBuildedEvent;
use Alahaxe\SimpleTextMatcher\Events\EngineStartedEvent;
use Alahaxe\SimpleTextMatcher\Events\EntitiesExtractedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageClassifiedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageCorrectedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageReceivedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageSplittedEvent;
use Alahaxe\SimpleTextMatcher\Events\ModelExpandedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class TestEventSubscriber
 * @package alahaxe\SimpleTextMatcher\Tests
 */
class TestEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Event[]
     */
    protected $collectedEvents = [];

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            BeforeModelBuildEvent::class => [
                'onEvent'
            ],
            EngineBuildedEvent::class => [
                'onEvent'
            ],
            EngineStartedEvent::class => [
                'onEvent'
            ],
            MessageReceivedEvent::class => [
                'onEvent'
            ],
            MessageCorrectedEvent::class => [
                'onEvent'
            ],
            MessageClassifiedEvent::class => [
                'onEvent'
            ],
            EntitiesExtractedEvent::class => [
                'onEvent'
            ],
            ModelExpandedEvent::class => [
                'onEvent'
            ],
            MessageSplittedEvent::class => [
                'onEvent'
            ]
        ];
    }

    /**
     * @param Event $e
     */
    public function onEvent(Event $e)
    {
        $this->collectedEvents[] = $e;
    }

    /**
     * @return Event[]
     */
    public function getCollectedEvents(): array
    {
        return $this->collectedEvents;
    }
}
