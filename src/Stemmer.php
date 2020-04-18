<?php

namespace alahaxe\SimpleTextMatcher;

use TextAnalysis\Stemmers\SnowballStemmer;

/**
 * Class Stemmer
 * @package alahaxe\SimpleTextMatcher\Classifiers
 */
class Stemmer extends SnowballStemmer
{
    /**
     * @var string[]
     */
    protected $cache = [];

    /**
     * @var string|null
     */
    protected $cacheFilePath;

    /**
     * @var bool
     */
    protected $needToWriteCache = false;

    /**
     * Stemmer constructor.
     * @param string|null $cacheFilePath
     * @param string $stemmerType
     */
    public function __construct(string $cacheFilePath = null, $stemmerType = 'French')
    {
        parent::__construct($stemmerType);

        if ($cacheFilePath !== null && file_exists($cacheFilePath)) {
            $this->cache = json_decode(file_get_contents($cacheFilePath), true) ?? [];
        }

        $this->cacheFilePath = $cacheFilePath;
    }

    /**
     *
     */
    public function writeCache():void
    {
        if ($this->cacheFilePath !== null && $this->needToWriteCache) {
            file_put_contents($this->cacheFilePath, json_encode($this->cache));
            $this->needToWriteCache = false;
        }
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

        $this->needToWriteCache = true;
        return $this->cache[$token] = parent::stem($token);
    }

    /**
     * @param string $phrase
     * @return string
     */
    public function stemPhrase(string $phrase):string
    {
        if (isset($this->cache[$phrase])) {
            return $this->cache[$phrase];
        }

        return $this->cache[$phrase] = implode(' ', array_map(function(string $word) {

            return $this->stem($word);
        }, array_filter(explode(' ', preg_replace('/\s+/', ' ', $phrase)), static function ($word) {

            return strlen($word) > 1;
        })));
    }

    /**
     * @return string[]
     */
    public function getCache(): array
    {
        return $this->cache;
    }
}
