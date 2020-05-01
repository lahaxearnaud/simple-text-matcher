<?php

namespace Alahaxe\SimpleTextMatcher;

use TextAnalysis\Stemmers\SnowballStemmer;

/**
 * Class Stemmer
 *
 * @package Alahaxe\SimpleTextMatcher\Classifiers
 */
class Stemmer extends SnowballStemmer
{
    /**
     * @var string[]
     */
    protected $cache = [];

    /**
     * Stemmer constructor.
     *
     * @param string $stemmerType
     */
    public function __construct($stemmerType = 'French')
    {
        parent::__construct($stemmerType);
    }

    /**
     * @inheritDoc
     */
    public function stem($token)
    {
        $token = trim($token);

        if (isset($this->cache[$token])) {
            return $this->cache[$token];
        }

        return $this->cache[$token] = parent::stem($token);
    }

    /**
     * @param  string $phrase
     * @return string
     */
    public function stemPhrase(string $phrase):string
    {
        if (isset($this->cache[$phrase])) {
            return $this->cache[$phrase];
        }

        return $this->cache[$phrase] = implode(
            ' ',
            array_map(
                function (string $word) {

                    return $this->stem($word);
                },
                array_filter(
                    StringUtils::words(preg_replace('/\s+/', ' ', $phrase)),
                    static function ($word) {

                        return strlen($word) > 1;
                    }
                )
            )
        );
    }

    /**
     * @return string[]
     */
    public function getCache(): array
    {
        return $this->cache;
    }

    /**
     * @param string[] $cache
     */
    public function setCache(array $cache): void
    {
        $this->cache = $cache;
    }
}
