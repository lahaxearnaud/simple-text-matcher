<?php

namespace Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex;

use Alahaxe\SimpleTextMatcher\Entities\EntityBag;

/**
 * Class SwiftBicExtractor
 *
 * @package Alahaxe\SimpleTextMatcher\Entities
 */
class SwiftBicExtractor extends AbstractRegexExtractor
{
    /**
     * @return array
     */
    public function getRegexes(): array
    {
        return [
            '/[a-z]{4}[ -]?([a-z0-9]{2}[ -]?){2}[a-z0-9]{0,3}/i',
        ];
    }

    /**
     * @inheritDoc
     */
    public function normalizeValue(string $rawValue): string
    {
        return \mb_strtoupper(str_replace([' ', '-'], '', $rawValue));
    }

    /**
     * @return string
     */
    public function getTypeExtracted(): string
    {
        return 'SWIFT_BIC';
    }
}
