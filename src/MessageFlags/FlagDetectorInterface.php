<?php

namespace Alahaxe\SimpleTextMatcher\MessageFlags;

use Alahaxe\SimpleTextMatcher\Message;

interface FlagDetectorInterface
{
    /**
     * @param Message $question
     * @return bool
     */
    public function detect(Message $question):bool;

    /**
     * @return string
     */
    public function getFlagName():string;
}
