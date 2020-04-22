<?php

namespace Alahaxe\SimpleTextMatcher\Events;

use Alahaxe\SimpleTextMatcher\Engine;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event is triggered when models are builded/loaded and the engine is ready to classify
 *
 * @package Alahaxe\SimpleTextMatcher\Events
 */
class EngineStartedEvent extends Event
{
    /**
     * @var Engine
     */
    protected $engine;

    /**
     * EngineBuildEvent constructor.
     *
     * @param Engine $engine
     */
    public function __construct(Engine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * @return Engine
     */
    public function getEngine(): Engine
    {
        return $this->engine;
    }
}
