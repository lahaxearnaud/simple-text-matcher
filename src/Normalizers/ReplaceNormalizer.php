<?php

namespace alahaxe\SimpleTextMatcher\Normalizers;

/**
 * Class ReplaceNormalizer
 *
 * @package alahaxe\SimpleTextMatcher\Normalizer
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
        $words = explode(' ', $rawText.' ');
        foreach ($words as $index => $word) {
            if (isset($this->replacements[$word])) {
                $words[$index] = $this->replacements[$word];
            }
        }

        return trim(implode(' ', $words));
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
