<?php

namespace Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex;

/**
 * Class HashtagExtractor
 *
 * @package Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex
 */
class HashtagExtractor extends AbstractRegexExtractor
{

    /**
     * @return array
     */
    public function getRegexes(): array
    {
        return [
            '/#[a-z0-9]*/i'
        ];
    }

    /**
     * @return string
     */
    public function getTypeExtracted(): string
    {
        return 'HASHTAG';
    }
}
