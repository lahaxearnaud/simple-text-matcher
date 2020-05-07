<?php

namespace Alahaxe\SimpleTextMatcher\MessageFlags\Detectors;

use Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\CancelExtractor;
use Alahaxe\SimpleTextMatcher\Message;

/**
 * Class CancelFlagDetector
 *
 * @package Alahaxe\SimpleTextMatcher\MessageFlags
 */
class CancelFlagDetector extends AbstractFlagDetector
{
    /**
     * @var CancelExtractor
     */
    protected $insultExtractor;

    /**
     * CancelFlagDetector constructor.
     *
     * @param string $lang
     */
    public function __construct(string $lang = 'fr')
    {
        $this->insultExtractor = new CancelExtractor($lang);
    }

    /**
     * @param Message $question
     * @return bool
     */
    public function detect(Message $question): bool
    {
        return !$this->insultExtractor->extract($question->getRawMessage())->isEmpty();
    }

    /**
     * @return string
     */
    public static function getFlagName(): string
    {
        return 'CANCEL';
    }
}
