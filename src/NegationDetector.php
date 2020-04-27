<?php

namespace Alahaxe\SimpleTextMatcher;

/**
 * Class NegationDetector
 *
 * @package Alahaxe\SimpleTextMatcher
 */
class NegationDetector {

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
    public function isSentenceWithNegation(Message $question):bool
    {
        foreach ($this->regexes as $regex) {
            if (preg_match($regex, $question->getRawMessage())) {
                return true;
            }
        }

        return false;
    }
}
