<?php

namespace Alahaxe\SimpleTextMatcher\Events;

use Alahaxe\SimpleTextMatcher\Engine;
use Alahaxe\SimpleTextMatcher\Message;

/**
 * Interface MessageEvent
 * @package Alahaxe\SimpleTextMatcher\Events
 */
interface MessageEvent
{
    /**
     * @return Engine
     */
    public function getMessage():Message;
}
