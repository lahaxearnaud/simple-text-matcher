<?php

namespace Alahaxe\SimpleTextMatcher\Subscribers;

use Alahaxe\SimpleTextMatcher\Events\EngineBuildedEvent;
use Alahaxe\SimpleTextMatcher\Events\EngineStartedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ModelCacheSubscriber
 *
 * @package Alahaxe\SimpleTextMatcher\Subscribers
 */
class ModelCacheSubscriber implements EventSubscriberInterface
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
            $event->getEngine()->setClassifierTrainedModels($cache['trainedModels'] ?? []);
            $event->getEngine()->setModel($cache['model'] ?? []);
            $event->getEngine()->setModelSignature($cache['modelSignature'] ?? 0);
        }
    }

    /**
     * @param EngineStartedEvent $event
     */
    public function onEngineStarted(EngineStartedEvent $event)
    {
        $engine = $event->getEngine();
        file_put_contents(
            $this->cacheFilePath,
            json_encode(
                [
                    'modelSignature' => $engine->getModelSignature(),
                    'trainedModels' => $engine->exportTrainedModels(),
                    'model' => $engine->getModel()
                ],
                JSON_PRETTY_PRINT
            )
        );
    }
}
