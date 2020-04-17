<?php

namespace alahaxe\SimpleTextMatcher\Tests\Normalizers;

use alahaxe\SimpleTextMatcher\Normalizers\QuotesNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;

/**
 * Class QuotesNormalizerTest
 * @package alahaxe\SimpleTextMatcher\Tests\Normalizers
 */
class QuotesNormalizerTest extends AbstractNormalizerTest
{

    /**
     * @inheritDoc
     */
    protected function setUp():void
    {
        parent::setUp();

        $this->normalizer = new QuotesNormalizer();
    }

    /**
     * @return array
     */
    public function correctProvider()
    {
        return [
            ["c'est", 'c est'],
            ["'\"", ''],
        ];
    }
}
