<?php

namespace alahaxe\SimpleTextMatcher\Normalizers;

/**
 * Class LowerCaseNormalizer
 *
 * @package alahaxe\SimpleTextMatcher\Normalizer
 */
class LowerCaseNormalizer implements NormalizerInterface
{
    /**
     * @param string $rawText
     *
     * @return string
     */
    public function normalize(string $rawText): string
    {
        return mb_strtolower($rawText);
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
