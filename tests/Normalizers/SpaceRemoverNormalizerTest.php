<?php

namespace Alahaxe\SimpleTextMatcher\Tests\Normalizers;

use Alahaxe\SimpleTextMatcher\Normalizers\SpaceRemoverNormalizer;

/**
 * Class SpaceRemoverNormalizerTest
 *
 * @package Alahaxe\SimpleTextMatcher\Tests\Normalizers
 */
class SpaceRemoverNormalizerTest extends AbstractNormalizerTest
{

    /**
     * @inheritDoc
     */
    protected function setUp():void
    {
        parent::setUp();

        $this->normalizer = new SpaceRemoverNormalizer();
    }

    /**
     * @return string[][]
     *
     * @psalm-return array{0: array{0: string, 1: string}, 1: array{0: string, 1: string}}
     */
    public function correctProvider()
    {
        return [
            ["je vais manger ", 'jevaismanger'],
            [" ", ''],
        ];
    }
}
