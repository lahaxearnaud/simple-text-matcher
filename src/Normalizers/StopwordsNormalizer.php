<?php

namespace alahaxe\SimpleTextMatcher\Normalizers;

use voku\helper\StopWords;
use voku\helper\StopWordsLanguageNotExists;

/**
 * Class StopwordsNormalizer
 *
 * @package alahaxe\SimpleTextMatcher\Normalizer
 */
class StopwordsNormalizer implements NormalizerInterface
{
    /**
     * @var array
     */
    protected $stopwords;

    /**
     * StopwordsNormalizer constructor.
     *
     * @param string $lang
     */
    public function __construct(string $lang = 'fr')
    {
        try {
            $this->stopwords = (new StopWords())->getStopWordsFromLanguage($lang);
        } catch (StopWordsLanguageNotExists $e) {
            $this->stopwords = [];
        }
    }


    /**
     * @param string $rawText
     *
     * @return string
     */
    public function normalize(string $rawText): string
    {
        if (empty($this->stopwords)) {
            return $rawText;
        }

        $words = explode(' ', $rawText.' ');
        foreach ($words as $index => $word) {
            if (in_array($word, $this->stopwords)) {
                unset($words[$index]);
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
        return 100;
    }

    /**
     * @return array
     */
    public function getStopwords(): array
    {
        return $this->stopwords;
    }
}
