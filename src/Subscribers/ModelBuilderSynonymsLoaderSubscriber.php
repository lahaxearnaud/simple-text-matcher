<?php

namespace Alahaxe\SimpleTextMatcher\Subscribers;

use Alahaxe\SimpleTextMatcher\Events\BeforeModelBuildEvent;
use Alahaxe\SimpleTextMatcher\Events\EngineBuildedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ModelBuilderSynonymsLoaderSubscriber
 *
 * @package Alahaxe\SimpleTextMatcher\Subscribers
 */
class ModelBuilderSynonymsLoaderSubscriber implements EventSubscriberInterface
{

    /**
     * @var string
     */
    protected $synonymsFileFolders;

    /**
     * ModelBuilderSynonymsLoaderSubscriber constructor.
     *
     * @param string $synonymsFileFolders
     */
    public function __construct(string $synonymsFileFolders = null)
    {
        if (is_null($synonymsFileFolders)) {
            $synonymsFileFolders = __DIR__.'/../../Resources/dataset';
        }

        $this->synonymsFileFolders = $synonymsFileFolders;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            BeforeModelBuildEvent::class => [
                'onBeforeModelBuildEvent'
            ],
            EngineBuildedEvent::class => [
                'onEngineBuildedEvent'
            ],
        ];
    }

    /**
     * @param BeforeModelBuildEvent $event
     */
    public function onBeforeModelBuildEvent(BeforeModelBuildEvent $event)
    {

        $modelBuilder = $event->getEngine()->getModelBuilder();
        $synonymsFile = $this->synonymsFileFolders.'/'.$modelBuilder->getLang().'/synonyms.json';

        if (file_exists($synonymsFile) && is_readable($synonymsFile)) {
            $synonyms = json_decode(file_get_contents($synonymsFile), true);
            if(!is_array($synonyms)) {
                return;
            }

            $modelBuilder->setGlobalLanguageSynonyms($synonyms);
        }
    }

    /**
     * @param EngineBuildedEvent $event
     */
    public function onEngineBuildedEvent(EngineBuildedEvent $event)
    {
        // free memory
        $event->getEngine()->getModelBuilder()->setGlobalLanguageSynonyms([]);
    }
}
