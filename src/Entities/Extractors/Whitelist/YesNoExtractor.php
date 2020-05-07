<?php

namespace Alahaxe\SimpleTextMatcher\Entities\Extractors\Whitelist;

use Alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use Alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;

/**
 *
 * @package Alahaxe\SimpleTextMatcher\Entities
 */
class YesNoExtractor extends WhiteListExtractor
{
    /**
     * YesNoExtractor constructor.
     *
     * @param string|null $yesNoFilePath
     */
    public function __construct(string $yesNoFilePath = null)
    {
        $yesNoFilePath = $yesNoFilePath ?? __DIR__ . '/../../../../Resources/dataset/fr/yesno.php';

        $languages = [];
        if (file_exists($yesNoFilePath) && is_readable($yesNoFilePath)) {
            $yesNo = include $yesNoFilePath;
        }

        parent::__construct('YESNO', $yesNo);

        $this->normalizers = new NormalizersBag();
        $this->normalizers
            ->add(new LowerCaseNormalizer())
            ->add(new UnaccentNormalizer())
        ;
    }
}
