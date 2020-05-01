<?php

namespace Alahaxe\SimpleTextMatcher\Entities\Extractors\Whitelist;

use Alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use Alahaxe\SimpleTextMatcher\Normalizers\SingularizeNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;

/**
 * Country list are from https://github.com/umpirsky/language-list/tree/master/data
 *
 * @package Alahaxe\SimpleTextMatcher\Entities
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
        $languageFilePath = $languageFilePath ?? __DIR__ . '/../../../../Resources/dataset/fr/language.php';

        $languages = [];
        if (file_exists($languageFilePath) && is_readable($languageFilePath)) {
            $languages = array_flip(include $languageFilePath);
        }

        parent::__construct('LANGUAGE', $languages);

        $this->normalizers = new NormalizersBag();
        $this->normalizers
            ->add(new LowerCaseNormalizer())
            ->add(new UnaccentNormalizer())
        ;
    }
}
