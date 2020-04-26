<?php

namespace Alahaxe\SimpleTextMatcher;

/**
 * Class QuestionSplitter
 *
 * @package Alahaxe\SimpleTextMatcher
 */
class QuestionSplitter
{
    /**
     *
     * The split word must be in the middle of the question +/- $searchRange %
     *
     * @var float
     */
    protected $searchRange;

    /**
     * @var int
     */
    protected $minimumCharFomEdge = 15;

    /**
     * @var int
     */
    protected $minimumSizeToSplit = 30;

    /**
     * @var array
     */
    protected $splitWords = [
        'et',
        'mais aussi',
        'puis',
        'and',
        'then',
    ];

    /**
     * QuestionSplitter constructor.
     *
     * @param array $splitWords
     * @param float $searchRange
     */
    public function __construct(array $splitWords = [], float $searchRange = 0.25)
    {
        $this->splitWords += $splitWords;
        $this->searchRange = $searchRange;
    }

    /**
     * @param Message $message
     *
     * @return string[]
     */
    public function splitQuestion(Message $message):array
    {
        $result = [];

        $rawMessage = $message->getRawMessage();
        $messageLength = strlen($rawMessage);

        if ($messageLength < $this->minimumSizeToSplit) {
            return [$message];
        }

        $nbCharsMiddle = floor($messageLength / 2);
        $minAcceptable =  $nbCharsMiddle * (1 - $this->searchRange);
        $maxAcceptable = $nbCharsMiddle * (1 + $this->searchRange);

        foreach ($this->splitWords as $splitWord) {
            $splitWordPosition = strpos($rawMessage, $splitWord);
            if ($splitWordPosition === false) {
                continue;
            }

            if ($splitWordPosition < $this->minimumCharFomEdge
                || ($splitWordPosition + $this->minimumCharFomEdge) > $messageLength
            ) {
                continue;
            }

            if ($splitWordPosition >= $minAcceptable && $splitWordPosition <= $maxAcceptable) {
                $result[] = new Message(substr($rawMessage, 0, $splitWordPosition));
                $result[] = new Message(substr($rawMessage, $splitWordPosition + strlen($splitWord)));
            }
        }

        if (empty($result)) {
            $result[] = $message;
        }

        return $result;
    }
}
