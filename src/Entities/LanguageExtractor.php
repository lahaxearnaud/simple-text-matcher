<?php

namespace alahaxe\SimpleTextMatcher\Entities;

/**
 * Country list are from https://github.com/umpirsky/language-list/tree/master/data
 *
 * @package alahaxe\SimpleTextMatcher\Entities
 */
class LanguageExtractor extends WhiteListExtractor
{
    /**
     * LanguageExtractor constructor.
     *
     * @param string|null $languageFilePath
     */
    public function __construct(string $languageFilePath = null)
    {
        $languageFilePath = $languageFilePath ?? __DIR__.'/../../Resources/dataset/fr/language.php';

        $languages = [];
        if (file_exists($languageFilePath) && is_readable($languageFilePath)) {
            $currencies = array_reverse(include $languageFilePath);
        }

        parent::__construct('LANGUAGE', $languages);
    }
}
