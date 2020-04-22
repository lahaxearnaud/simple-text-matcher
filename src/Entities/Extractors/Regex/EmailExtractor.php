<?php

namespace Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex;

/**
 * Class EmailExtractor
 * @package Alahaxe\SimpleTextMatcher\Entities
 */
class EmailExtractor extends AbstractRegexExtractor
{
    /**
     * @return string
     */
    public function getTypeExtracted(): string
    {
        return 'EMAIL';
    }

    /**
     * @return array
     */
    public function getRegexes(): array
    {
        return [
            "/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i"
        ];
    }
}
