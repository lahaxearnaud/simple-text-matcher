<?php

namespace Alahaxe\SimpleTextMatcher\Events;

use Alahaxe\SimpleTextMatcher\Engine;

/**
 * Interface EngineEvent
 * @package Alahaxe\SimpleTextMatcher\Events
 */
interface EngineEvent
{
    /**
     * @return Engine
     */
    public function getEngine():Engine;
}
