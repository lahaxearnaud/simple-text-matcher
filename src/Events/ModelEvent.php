<?php

namespace Alahaxe\SimpleTextMatcher\Events;

use Alahaxe\SimpleTextMatcher\Engine;

/**
 * Interface ModelEvent
 * @package Alahaxe\SimpleTextMatcher\Events
 */
interface ModelEvent
{
    /**
     * @return array
     */
    public function getModel():array;
}
