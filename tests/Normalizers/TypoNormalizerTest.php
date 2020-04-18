<?php

namespace alahaxe\SimpleTextMatcher\Tests\Normalizers;

use alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;

/**
 * Class TypoNormalizerTest
 *
 * @package alahaxe\SimpleTextMatcher\Tests\Normalizers
 */
class TypoNormalizerTest extends AbstractNormalizerTest
{

    /**
     * @inheritDoc
     */
    protected function setUp():void
    {
        parent::setUp();

        $this->normalizer = new TypoNormalizer(
            [
            'sncf'
            ]
        );
    }

    /**
     * @return array
     */
    public function correctProvider()
    {
        return [
            ['testt', 'test'],
            ['ceci est un testt', 'ceci est un test'],
            ['manger une pome', 'manger une pomme'],
            ['mnager uen pome', 'manger une pomme'],
            ['manger un plat maixicain', 'manger un plat mexicain'],
            ['mangre un plta maixicain', 'manger un plat mexicain'],

            // word that must not be fixed
            ['train de la SNCF', 'train de la SNCF'],
            ['train de la sncf', 'train de la sncf'],
        ];
    }
}
