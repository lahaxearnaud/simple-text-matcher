<?php

namespace Alahaxe\SimpleTextMatcher\Normalizers;

use Alahaxe\SimpleTextMatcher\StringUtils;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\Inflector\Language;

/**
 * Class LowerCaseNormalizer
 *
 * @package Alahaxe\SimpleTextMatcher\Normalizer
 */
class UnaccentNormalizer implements NormalizerInterface
{
    /**
     * @var Inflector
     */
    protected $inflector;

    /**
     * UnaccentNormalizer constructor.
     */
    public function __construct()
    {
        $inflectorFactory = new InflectorFactory();
        $this->inflector = $inflectorFactory(Language::FRENCH);
    }

    /**
     * @param string $rawText
     *
     * @return string
     */
    public function normalize(string $rawText): string
    {
        $words = StringUtils::words($rawText);
        foreach ($words as $index => $word) {
            $words[$index] = $this->inflector->unaccent($word);
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
        return 255;
    }
}
