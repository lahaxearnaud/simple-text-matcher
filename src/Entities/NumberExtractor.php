<?php


namespace alahaxe\SimpleTextMatcher\Entities;


class NumberExtractor extends AbstractRegexExtractor
{

    /**
     * @return array
     */
    public function getRegexes(): array
    {
        return [
            '/(?:[0-9]+\s*)+(?:dot|comma|virgule|point|[,\.])+\s*[0-9]*/i'
        ];
    }

    /**
     * @return string
     */
    public function getTypeExtracted(): string
    {
        return 'NUMBER';
    }

    /**
     * @param string $rawValue
     *
     * @return string
     */
    public function normalizeValue(string $rawValue): string
    {
        $rawValue = mb_strtolower($rawValue);
        $rawValue = str_replace([',', 'virgule', 'point', 'comma', 'dot'], '.', $rawValue);
        $rawValue = preg_replace('/[a-z\% ]/', '', $rawValue);

        return floatval($rawValue);
    }
}
