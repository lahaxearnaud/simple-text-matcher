<?php

namespace alahaxe\SimpleTextMatcher\Tests\Normalizers;

use alahaxe\SimpleTextMatcher\Normalizers\QuotesNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\StopwordsNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;

/**
 * Class StopWordNormalizerTest
 *
 * @package alahaxe\SimpleTextMatcher\Tests\Normalizers
 */
class StopWordNormalizerTest extends AbstractNormalizerTest
{

    /**
     * @inheritDoc
     */
    protected function setUp():void
    {
        parent::setUp();

        $this->normalizer = new StopwordsNormalizer();
    }

    /**
     * @return string[][]
     *
     * @psalm-return array{0: array{0: string, 1: string}}
     */
    public function correctProvider()
    {
        return [
            ["aller dans un magasin", 'aller magasin'],
        ];
    }

    public function testBadLanguage(): void
    {
        $normalier = new StopwordsNormalizer('zzz');
        $this->assertIsArray($normalier->getStopwords());
        $this->assertEmpty($normalier->getStopwords());

        $text = 'a le un mais but aussi';
        $this->assertEquals(
            $normalier->normalize($text),
            $text
        );
    }
}
