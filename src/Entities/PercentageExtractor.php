<?php

namespace alahaxe\SimpleTextMatcher\Entities;

/**
 * Class PhoneNumberExtractor
 *
 * @package alahaxe\SimpleTextMatcher\Entities
 */
class PercentageExtractor extends NumberExtractor
{
    /**
     * @return string
     */
    public function getTypeExtracted(): string
    {
        return 'PERCENTAGE';
    }

    /**
     * @return array
     */
    public function getRegexes(): array
    {
        return [
            '/[0-9,\.]+\s*(?:(point|virgule|comma|dot)\s*[0-9,\.]+\s*)?(?:\%|pourcentage|percent|pour\s?cent(s)?)/i',
        ];
    }
}
