<?php

namespace alahaxe\SimpleTextMatcher\Normalizers;

/**
 * Class UnpunctuateNormalizer
 * @package alahaxe\SimpleTextMatcher\Normalizer
 */
class UnpunctuateNormalizer implements NormalizerInterface
{
    /**
     * @param string $rawText
     *
     * @return string
     */
    public function normalize(string $rawText): string
    {
        return trim(preg_replace("/(?![=$'€%])\p{P}/u", '', $rawText));
    }

    /**
     * Priority the biggest will be the first to be applied
     *
     * @return int
     */
    public function getPriority(): int
    {
        return 253;
    }
}
