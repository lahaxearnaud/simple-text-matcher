<?php

namespace Alahaxe\SimpleTextMatcher\Normalizers;

use Alahaxe\SimpleTextMatcher\StringUtils;

/**
 * Class ReplaceNormalizer
 *
 * @package Alahaxe\SimpleTextMatcher\Normalizer
 */
class ReplaceNormalizer implements NormalizerInterface
{
    /**
     * @var array
     */
    protected $replacements = [];

    /**
     * ReplaceNormalizer constructor.
     * @param array $replacements
     */
    public function __construct(array $replacements = [])
    {
        $this->replacements = $replacements;
    }

    /**
     * @param string $rawText
     *
     * @return string
     */
    public function normalize(string $rawText): string
    {
        $words = StringUtils::words($rawText.' ');
        foreach ($words as $index => $word) {
            if (isset($this->replacements[$word])) {
                $words[$index] = $this->replacements[$word];
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
        return 250;
    }
}
