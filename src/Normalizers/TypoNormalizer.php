<?php

namespace Alahaxe\SimpleTextMatcher\Normalizers;

use Alahaxe\SimpleTextMatcher\StringUtils;

/**
 * Fix basic typo in a word
 *
 * @package Alahaxe\SimpleTextMatcher\Normalizer
 */
class TypoNormalizer implements NormalizerInterface
{

    /**
     * @var
     */
    protected $dictionary;

    /**
     * list of words that must not be corrected
     *
     * @var array
     */
    protected $ignoreWords = [];

    /**
     * TypoNormalizer constructor.
     *
     * @param array  $ignoreWords
     * @param string $language
     */
    public function __construct(array $ignoreWords = [], $language = 'fr')
    {
        $this->dictionary = pspell_new($language);

        $this->ignoreWords = array_map(
            static function ($word) {
                return strtolower($word);
            },
            $ignoreWords
        );
    }


    /**
     * @param string $rawText
     *
     * @return string
     */
    public function normalize(string $rawText): string
    {
        $words =StringUtils::words($rawText);
        foreach ($words as $index => $word) {
            if (in_array(strtolower($word), $this->ignoreWords, true)) {
                continue;
            }

            if (!pspell_check($this->dictionary, $word)) {
                $suggestions = pspell_suggest($this->dictionary, $word);
                $words[$index] = current($suggestions);
            }
        }

        return StringUtils::sentence($words);
    }

    /**
     * Priority the biggest will be the first to be applied
     *
     * @return int
     */
    public function getPriority(): int
    {
        return 245;
    }
}
