<?php

namespace alahaxe\SimpleTextMatcher\Tests\Normalizers;

use alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\QuotesNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\StopwordsNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;

/**
 * Class LowerCaseNormalizerTest
 * @package alahaxe\SimpleTextMatcher\Tests\Normalizers
 */
class LowerCaseNormalizerTest extends AbstractNormalizerTest
{

    /**
     * @inheritDoc
     */
    protected function setUp():void
    {
        parent::setUp();

        $this->normalizer = new LowerCaseNormalizer();
    }

    /**
     * @return array
     */
    public function correctProvider()
    {
        return [
            ["AEIOUY", 'aeiouy'],
            ["É", 'é'],
        ];
    }
}
