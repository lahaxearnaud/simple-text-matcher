<?php

namespace Alahaxe\SimpleTextMatcher\Tests\Normalizers;

use Alahaxe\SimpleTextMatcher\Normalizers\QuotesNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;

/**
 * Class QuotesNormalizerTest
 *
 * @package Alahaxe\SimpleTextMatcher\Tests\Normalizers
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
     * @return string[][]
     *
     * @psalm-return array{0: array{0: string, 1: string}, 1: array{0: string, 1: string}}
     */
    public function correctProvider()
    {
        return [
            ["c'est", 'c est'],
            ["'\"", ''],
        ];
    }
}
