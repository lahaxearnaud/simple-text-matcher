<?php

namespace alahaxe\SimpleTextMatcher\Tests\Normalizers;

use alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;

/**
 * Class UnaccentNormalizerTest
 *
 * @package alahaxe\SimpleTextMatcher\Tests\Normalizers
 */
class UnaccentNormalizerTest extends AbstractNormalizerTest
{

    /**
     * @inheritDoc
     */
    protected function setUp():void
    {
        parent::setUp();

        $this->normalizer = new UnaccentNormalizer();
    }

    /**
     * @return string[][]
     *
     * @psalm-return array{0: array{0: string, 1: string}, 1: array{0: string, 1: string}, 2: array{0: string, 1: string}}
     */
    public function correctProvider()
    {
        return [
            ['mangé', 'mange'],
            ["c'est noël", "c'est noel"],
            ["aéèçàüû", "aeecauu"],
        ];
    }
}
