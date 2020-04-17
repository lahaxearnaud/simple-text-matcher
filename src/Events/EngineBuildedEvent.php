<?php

namespace alahaxe\SimpleTextMatcher\Events;

use alahaxe\SimpleTextMatcher\Engine;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class EngineBuildEvent
 * @package alahaxe\SimpleTextMatcher\Events
 */
class EngineBuildedEvent extends Event
{
    /**
     * @var Engine
     */
    protected $engine;

    /**
     * EngineBuildEvent constructor.
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
