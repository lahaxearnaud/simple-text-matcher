<?php

namespace alahaxe\SimpleTextMatcher\Tests\Normalizers;

use alahaxe\SimpleTextMatcher\Normalizers\SingularizeNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\UnpunctuateNormalizer;

/**
 * Class SingularizeNormalizerTest
 *
 * @package alahaxe\SimpleTextMatcher\Tests\Normalizers
 */
class SingularizeNormalizerTest extends AbstractNormalizerTest
{

    /**
     * @inheritDoc
     */
    protected function setUp():void
    {
        parent::setUp();

        $this->normalizer = new SingularizeNormalizer();
    }

    /**
     * @return string[][]
     *
     * @psalm-return array{0: array{0: string, 1: string}, 1: array{0: string, 1: string}}
     */
    public function correctProvider()
    {
        return [
            ['je mange des pommes', 'je mange de pomme'],
            ["les avions", "le avion"],
        ];
    }
}
