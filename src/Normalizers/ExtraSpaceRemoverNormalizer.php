<?php

namespace Alahaxe\SimpleTextMatcher\Normalizers;

/**
 * Class ExtraSpaceRemoverNormalizer
 *
 * @package Alahaxe\SimpleTextMatcher\Normalizer
 */
class ExtraSpaceRemoverNormalizer implements NormalizerInterface
{
    /**
     * @param string $rawText
     *
     * @return string
     */
    public function normalize(string $rawText): string
    {
        return trim(preg_replace('/\s+/', ' ', $rawText));
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
