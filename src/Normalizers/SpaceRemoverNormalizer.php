<?php

namespace Alahaxe\SimpleTextMatcher\Normalizers;

/**
 * Class SpaceRemoverNormalizer
 *
 * @package Alahaxe\SimpleTextMatcher\Normalizer
 */
class SpaceRemoverNormalizer implements NormalizerInterface
{
    /**
     * @param string $rawText
     *
     * @return string
     */
    public function normalize(string $rawText): string
    {
        return str_replace(' ', '',$rawText);
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
