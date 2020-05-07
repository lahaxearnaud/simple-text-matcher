<?php

namespace Alahaxe\SimpleTextMatcher\Subscribers;

use Alahaxe\SimpleTextMatcher\Events\MessageClassifiedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class EntitySubscriber
 * @package Alahaxe\SimpleTextMatcher\Subscribers
 */
class EntitySubscriber implements EventSubscriberInterface
{

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            MessageClassifiedEvent::class => [
                'onMessageClassified'
            ],
        ];
    }

    /**
     * @param MessageClassifiedEvent $event
     *
     * @return void
     */
    public function onMessageClassified(MessageClassifiedEvent $event): void
    {
        $engine = $event->getEngine();
        $question = $event->getMessage();

        $intent = $question->getIntentDetected();

        if ($intent === null) {
            return;
        }

        $intentExtractors = $engine->getIntentExtractors();
        if (isset($intentExtractors[$question->getIntentDetected()])) {
            $question->setEntities(
                $engine->getExtractors()->getByTypes($intentExtractors[$intent])->apply($question->getRawMessage(), $intentExtractors[$intent])
            );
        }

    }
}
