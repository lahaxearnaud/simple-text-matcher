<?php

namespace alahaxe\SimpleTextMatcher\Entities;

/**
 * Class UrlExtractor
 * @package alahaxe\SimpleTextMatcher\Entities
 */
class UrlExtractor extends AbstractRegexExtractor
{
    /**
     * @return string
     */
    public function getTypeExtracted(): string
    {
        return 'URL';
    }

    /**
     * @return array
     */
    public function getRegexes(): array
    {
        return [
            "#(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))#iS"
        ];
    }
}
