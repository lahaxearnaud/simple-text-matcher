<?php


namespace Alahaxe\SimpleTextMatcher\Subscribers;


use Alahaxe\SimpleTextMatcher\Classifiers\ClassificationResultsBag;
use Alahaxe\SimpleTextMatcher\Events\MessageCorrectedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ClassificationSubscriber implements EventSubscriberInterface
{

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            MessageCorrectedEvent::class => [
                'onMessageCorrected'
            ],
        ];
    }

    /**
     * @param MessageCorrectedEvent $event
     *
     * @return void
     */
    public function onMessageCorrected(MessageCorrectedEvent $event): void
    {
        $engine = $event->getEngine();
        $question = $event->getMessage();

        $bag = new ClassificationResultsBag();
        foreach ($engine->getClassifiers()->all() as $classifier) {
            $bag->merge($classifier->classify($question));

            // classifier score is at max, no need to continue
            if ($bag->getResultsWithMinimumScore(1.)->count() > 0) {
                break;
            }
        }

        $question->setClassification($bag->getTopIntents(3, 0.3));

        $bestResult = $bag->offsetGet(0);
        if ($bestResult !== null) {
            $intent = $bestResult->getIntent();
            $question->setIntentDetected($intent);
        }
    }
}
