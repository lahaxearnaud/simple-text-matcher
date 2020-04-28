<?php

namespace Alahaxe\SimpleTextMatcher\MessageFlags\Detectors;

use Alahaxe\SimpleTextMatcher\Message;
use Alahaxe\SimpleTextMatcher\MessageFlags\Flag;

/**
 * Interface FlagDetectorInterface
 * @package Alahaxe\SimpleTextMatcher\MessageFlags
 */
interface FlagDetectorInterface
{
    /**
     * @param Message $question
     * @return bool
     */
    public function detect(Message $question):bool;

    /**
     * @return Flag
     */
    public function buildFlag():Flag;
}
