<?php

namespace Alahaxe\SimpleTextMatcher;

/**
 * Class StringUtil
 *
 * @package Alahaxe\SimpleTextMatcher
 */
class StringUtils
{
    /**
     * @param string $sentence
     * @return array
     */
    public static function words(string $sentence):array
    {
        return explode(' ', $sentence);
    }

    /**
     * @param array $words
     * @return string
     */
    public static function sentence(array $words):string
    {
        return trim(implode(' ', $words));
    }
}
