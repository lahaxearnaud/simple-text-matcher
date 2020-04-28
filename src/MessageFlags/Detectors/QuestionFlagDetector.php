<?php

namespace Alahaxe\SimpleTextMatcher\MessageFlags\Detectors;

use Alahaxe\SimpleTextMatcher\Message;

/**
 * Class NegationFlagDetector
 *
 * @package Alahaxe\SimpleTextMatcher\MessageFlags
 */
class QuestionFlagDetector extends AbstractFlagDetector
{
    /**
     * @var string[]
     */
    protected $regexes = [
        "/\?/", // question mark

        "/(com(bien|ment)|[a-z]\-(nous|vous|tu|je)|qu(e|el(s|le(s)?)?|i|oi)|quand|est\-ce|pourquoi|où)\s/i", // fr

        "/^((do|did|is|are|can|may)|(how|why|where|what))(\s|\')/i", // simple en
        "/(is|are|wo|have|has|can|shall|might|do|does|did)(n\'t|\snot)?\s(it|I|you|she|they|we)\s?\??$/i", // en question tag
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
}
