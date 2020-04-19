<?php

namespace alahaxe\SimpleTextMatcher\Normalizers;

/**
 * Class QuotesNormalizer
 *
 * @package alahaxe\SimpleTextMatcher\Normalizer
 */
class QuotesNormalizer implements NormalizerInterface
{
    /**
     * @param string $rawText
     *
     * @return string
     */
    public function normalize(string $rawText): string
    {
        return trim(str_replace(['"', "'"], ' ', $rawText));
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
