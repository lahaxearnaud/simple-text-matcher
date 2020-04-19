<?php

namespace alahaxe\SimpleTextMatcher\Normalizers;

/**
 * Fix basic typo in a word
 *
 * @package alahaxe\SimpleTextMatcher\Normalizer
 */
class TypoNormalizer implements NormalizerInterface
{

    /**
     * @var
     */
    protected $dictonnary;

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
        $this->dictonnary = pspell_new('fr');

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
        $words = explode(' ', $rawText.' ');
        foreach ($words as $index => $word) {
            if (in_array(strtolower($word), $this->ignoreWords, true)) {
                continue;
            }

            if (!pspell_check($this->dictonnary, $word)) {
                $suggestions = pspell_suggest($this->dictonnary, $word);
                $words[$index] = current($suggestions);
            }
        }

        return trim(implode(' ', $words));
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
