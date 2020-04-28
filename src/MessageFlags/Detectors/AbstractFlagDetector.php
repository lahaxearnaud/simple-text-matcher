<?php

namespace Alahaxe\SimpleTextMatcher\MessageFlags\Detectors;

use Alahaxe\SimpleTextMatcher\MessageFlags\Flag;

/**
 * Class AbstractFlagDetector
 * @package Alahaxe\SimpleTextMatcher\MessageFlags
 */
abstract class AbstractFlagDetector implements FlagDetectorInterface
{
    /**
     * @return Flag
     */
    public function buildFlag(): Flag
    {
        return new Flag(get_class($this));
    }
}
