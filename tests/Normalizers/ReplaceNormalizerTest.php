<?php

namespace alahaxe\SimpleTextMatcher\Tests\Normalizers;

use alahaxe\SimpleTextMatcher\Normalizers\ReplaceNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\UnpunctuateNormalizer;

/**
 * Class UnpunctuateNormalizerTest
 *
 * @package alahaxe\SimpleTextMatcher\Tests\Normalizers
 */
class ReplaceNormalizerTest extends AbstractNormalizerTest
{

    /**
     * @inheritDoc
     */
    protected function setUp():void
    {
        parent::setUp();

        $this->normalizer = new ReplaceNormalizer([
            'foo' => 'bar',
            'fooBar' => 'barFoo'
        ]);
    }

    /**
     * @return string[][]
     *
     * @psalm-return array{0: array{0: string, 1: string}, 1: array{0: string, 1: string}}
     */
    public function correctProvider()
    {
        return [
            ['Hello foo', 'Hello bar'],
            ['Hello fooBar', 'Hello barFoo'],
            ["Hello world", "Hello world"],
        ];
    }
}
