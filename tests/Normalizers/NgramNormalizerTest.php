<?php

namespace Alahaxe\SimpleTextMatcher\Tests\Normalizers;

use Alahaxe\SimpleTextMatcher\Normalizers\NgramNormalizer;

/**
 * Class NgramNormalizerTest
 *
 * @package Alahaxe\SimpleTextMatcher\Tests\Normalizers
 */
class NgramNormalizerTest extends AbstractNormalizerTest
{

    /**
     * @inheritDoc
     */
    protected function setUp():void
    {
        parent::setUp();

        $this->normalizer = new NgramNormalizer();
    }

    /**
     * @return string[][]
     *
     * @psalm-return array{0: array{0: string, 1: string}, 1: array{0: string, 1: string}}
     */
    public function correctProvider()
    {
        return [
            ["je vais manger des pommes bien mures", 'jevaismanger vaismangerdes mangerdespommes despommesbien pommesbienmures'],
            ["je vais", ""],
            ["je vais à la campagne", "jevaisà vaisàla àlacampagne"],
        ];
    }
}
