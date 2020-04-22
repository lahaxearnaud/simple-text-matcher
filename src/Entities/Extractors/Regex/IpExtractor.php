<?php

namespace Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex;

/**
 * Class IpExtractor
 * @package Alahaxe\SimpleTextMatcher\Entities
 */
class IpExtractor extends AbstractRegexExtractor
{

    /**
     * @return array
     */
    public function getRegexes(): array
    {
        return [
            // ip v4
            '/(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)/',
            // ip v6
            '/(((?=(?>.*?(::))(?!.+\3)))\3?|([\dA-F]{1,4}(\3|:(?!$)|$)|\2))(?4){5}((?4){2}|((2[0-4]|1\d|[1-9])?\d|25[0-5])(\.(?7)){3})\z/i'
        ];
    }

    /**
     * @return string
     */
    public function getTypeExtracted(): string
    {
        return 'IP';
    }
}
