<?php


namespace Alahaxe\SimpleTextMatcher\Subscribers;

use Alahaxe\SimpleTextMatcher\Events\EntitiesExtractedEvent;
use Alahaxe\SimpleTextMatcher\Handlers\AbstractHandler;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class HandlerSubscriber
 *
 * @package Alahaxe\SimpleTextMatcher\Subscribers
 */
class HandlerSubscriber implements EventSubscriberInterface
{
    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * HandlerSubscriber constructor.
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            EntitiesExtractedEvent::class => [
                'onEntitiesExtractedEvent'
            ]
        ];
    }

    /**
     * @param EntitiesExtractedEvent $event
     */
    public function onEntitiesExtractedEvent(EntitiesExtractedEvent $event)
    {
        $message = $event->getMessage();

        if ($message->getIntentDetected() === null) {
            $this->eventDispatcher->dispatch($message, 'intent.'.AbstractHandler::DEFAULT_INTENT_NAME);

            return;
        }

        $this->eventDispatcher->dispatch($message, 'intent.'.$message->getIntentDetected());

        if (!$message->hasResponses()) {
            $this->eventDispatcher->dispatch($message, 'intent.'.AbstractHandler::DEFAULT_INTENT_NAME);
        }
    }
}
