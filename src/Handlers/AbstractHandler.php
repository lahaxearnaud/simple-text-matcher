<?php

namespace Alahaxe\SimpleTextMatcher\Handlers;

use Alahaxe\SimpleTextMatcher\Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AbstractHandler
 * @package Alahaxe\SimpleTextMatcher\Handlers
 */
abstract class AbstractHandler implements EventSubscriberInterface
{
    const DEFAULT_INTENT_NAME = 'default';
    const INSULT_INTENT_NAME = 'insult';

    /**
     * @return string
     */
    protected abstract static function intentName():string;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'intent.'.static::intentName() => [
                'handle'
            ]
        ];
    }

    /**
     * @param Message $message
     *
     * @return mixed
     */
    public abstract function handle(Message $message);
}
