<?php

namespace alahaxe\SimpleTextMatcher\Tests\Normalizers;

use alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\UnpunctuateNormalizer;

/**
 * Class UnpunctuateNormalizerTest
 * @package alahaxe\SimpleTextMatcher\Tests\Normalizers
 */
class UnpunctuateNormalizerTest extends AbstractNormalizerTest
{

    /**
     * @inheritDoc
     */
    protected function setUp():void
    {
        parent::setUp();

        $this->normalizer = new UnpunctuateNormalizer();
    }

    /**
     * @return array
     */
    public function correctProvider()
    {
        return [
            ['Hello, je suis', 'Hello je suis'],
            [", . ? ! - _ ( ) \ / ", ""],
        ];
    }
}
