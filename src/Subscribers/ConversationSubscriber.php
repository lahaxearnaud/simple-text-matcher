<?php


namespace Alahaxe\SimpleTextMatcher\Subscribers;


use Alahaxe\SimpleTextMatcher\Conversation\Conversation;
use Alahaxe\SimpleTextMatcher\Events\ConversationMessageReceivedEvent;
use Alahaxe\SimpleTextMatcher\Events\EntitiesExtractedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageRespondedEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ConversationSubscriber
 *
 * @package Alahaxe\SimpleTextMatcher\Subscribers
 */
class ConversationSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            MessageRespondedEvent::class => [
                'onMessageResponded'
            ],
            ConversationMessageReceivedEvent::class => [
                'onConversationMessageReceived'
            ]
        ];
    }

    /**
     * @param ConversationMessageReceivedEvent $event
     */
    public function onConversationMessageReceived(ConversationMessageReceivedEvent $event)
    {
        $message = $event->getMessage();
        $engine = $event->getEngine();

        $conversation = $message->getConversation();

        // reload entities and intent in message
        $message->getEntities()->add($conversation->getEntities()->toArray());
        $message->setIntentDetected($conversation->getIntent());

        // bypass classification an trigger handler
        $engine->getEventDispatcher()->dispatch(new EntitiesExtractedEvent($message));
    }

    /**
     * @param MessageRespondedEvent $event
     */
    public function onMessageResponded(MessageRespondedEvent $event)
    {
        $message = $event->getMessage();
        if ($message->isExpectAnswer()) {
            $conversation = $message->getConversation();
            $conversation->getEntities()->add($message->getEntities()->toArray());
        } else {
            $message->setConversation(null);
        }
    }
}
