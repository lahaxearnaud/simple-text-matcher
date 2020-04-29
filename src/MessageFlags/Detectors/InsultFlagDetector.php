<?php


namespace Alahaxe\SimpleTextMatcher\MessageFlags\Detectors;

use Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\InsultExtractor;
use Alahaxe\SimpleTextMatcher\Message;

/**
 * Class InsultFlagDetector
 *
 * @package Alahaxe\SimpleTextMatcher\MessageFlags\Detectors
 */
class InsultFlagDetector extends AbstractFlagDetector
{

    /**
     * @var InsultExtractor
     */
    protected $insultExtractor;

    /**
     * InsultFlagDetector constructor.
     *
     * @param string $lang
     */
    public function __construct(string $lang = 'fr')
    {
        $this->insultExtractor = new InsultExtractor($lang);
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
        return 'INSULT';
    }
}
