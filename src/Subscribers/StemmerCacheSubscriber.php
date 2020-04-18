<?php

namespace alahaxe\SimpleTextMatcher\Subscribers;

use alahaxe\SimpleTextMatcher\Events\EngineBuildedEvent;
use alahaxe\SimpleTextMatcher\Events\EngineStartedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class StemmerCacheSubscriber
 *
 * @package alahaxe\SimpleTextMatcher\Subscribers
 */
class StemmerCacheSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $cacheFilePath;

    /**
     * StemmerCacheSubscriber constructor.
     *
     * @param string $cacheFilePath
     */
    public function __construct(string $cacheFilePath)
    {
        $this->cacheFilePath = $cacheFilePath;
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
        ];
    }

    /**
     * @param EngineBuildedEvent $event
     */
    public function onEngineBuilded(EngineBuildedEvent $event)
    {
        if (file_exists($this->cacheFilePath) && is_readable($this->cacheFilePath)) {
            $cache = json_decode(file_get_contents($this->cacheFilePath), true) ?? [];
            $event->getEngine()->getStemmer()->setCache($cache);
        }
    }

    /**
     * @param EngineStartedEvent $event
     */
    public function onEngineStarted(EngineStartedEvent $event)
    {
        $cache = $event->getEngine()->getStemmer()->getCache();
        file_put_contents($this->cacheFilePath, json_encode($cache, JSON_PRETTY_PRINT));
    }
}
