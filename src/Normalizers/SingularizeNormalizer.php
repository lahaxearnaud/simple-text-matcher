<?php

namespace Alahaxe\SimpleTextMatcher\Normalizers;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;

/**
 * Class SingularizeNormalizer
 * @package Alahaxe\SimpleTextMatcher\Normalizers
 */
class SingularizeNormalizer implements NormalizerInterface
{
    /**
     * @var Inflector
     */
    protected $inflector;

    /**
     * SingularizeNormalizer constructor.
     */
    public function __construct(string $language = 'french')
    {
        $inflectorFactory = new InflectorFactory();

        $this->inflector = $inflectorFactory($language);
    }

    /**
     * @param string $rawText
     *
     * @return string
     */
    public function normalize(string $rawText): string
    {
        $words = explode(' ', $rawText);
        foreach ($words as $index => $word) {
            $words[$index] = $this->inflector->singularize($word);
        }

        return implode(' ', $words);
    }

    /**
     * Priority the biggest will be the first to be applied
     *
     * @return int
     */
    public function getPriority(): int
    {
        return 230;
    }
}
