<?php

namespace Alahaxe\SimpleTextMatcher\Events;

use Alahaxe\SimpleTextMatcher\Engine;
use Alahaxe\SimpleTextMatcher\ModelBuilder;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class BeforeModelBuildEvent
 *
 * This event is trigger before the model builder start building
 *
 * @package Alahaxe\SimpleTextMatcher\Events
 */
class BeforeModelBuildEvent extends Event
{
    /**
     * @var Engine
     */
    protected $engine;

    /**
     * BeforeModelBuildEvent constructor.
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
