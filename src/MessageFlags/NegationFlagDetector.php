<?php

namespace Alahaxe\SimpleTextMatcher\MessageFlags;

use Alahaxe\SimpleTextMatcher\Message;

/**
 * Class NegationFlagDetector
 *
 * @package Alahaxe\SimpleTextMatcher\MessageFlags
 */
class NegationFlagDetector implements FlagDetectorInterface
{

    const NAME = 'negation';

    /**
     * @var string[]
     */
    protected $regexes = [
        "/\s(ne\s|n\').*\s(pas|guerre|point)\s/i",
        "/(\s(ni)\s.*){2}/i",
        "/\s(pas)\s/i",

        "/\s(do(es)? not|doesn\'t)\s/i",
    ];

    /**
     * @param Message $question
     * @return bool
     */
    public function detect(Message $question):bool
    {
        foreach ($this->regexes as $regex) {
            if (preg_match($regex, $question->getRawMessage())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getFlagName(): string
    {
        return self::NAME;
    }
}
