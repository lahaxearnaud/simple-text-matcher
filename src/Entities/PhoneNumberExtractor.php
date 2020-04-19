<?php

namespace alahaxe\SimpleTextMatcher\Entities;

/**
 * Class PhoneNumberExtractor
 * @package alahaxe\SimpleTextMatcher\Entities
 */
class PhoneNumberExtractor extends AbstractRegexExtractor
{
    /**
     * @return string
     */
    public function getTypeExtracted(): string
    {
        return 'PHONE_NUMBER';
    }

    /**
     * @return array
     */
    public function getRegexes(): array
    {
        return [
            '/(\+?)?[0-9\. \-]{9,15}[0-9]/', # simple
            '/^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/' # north us
        ];
    }

    public function normalizeValue(string $rawValue): string
    {
        return str_replace([' ', '.', '-'], '', $rawValue);
    }
}
