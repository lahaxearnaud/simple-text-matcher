<?php

namespace alahaxe\SimpleTextMatcher\Tests;

use alahaxe\SimpleTextMatcher\ModelBuilder;
use alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use alahaxe\SimpleTextMatcher\Normalizers\QuotesNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\StopwordsNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\UnpunctuateNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * Class ModelBuilderTest
 *
 * @package alahaxe\SimpleTextMatcher\Tests
 */
class ModelBuilderTest extends TestCase
{

    /**
     * @var ModelBuilder
     */
    protected $modelBuilder;

    protected function setUp():void
    {
        parent::setUp();

        $normalizerBag = new NormalizersBag();
        $normalizerBag
            ->add(new LowerCaseNormalizer())
            ->add(new StopwordsNormalizer())
            ->add(new UnaccentNormalizer())
            ->add(new UnpunctuateNormalizer())
            ->add(new QuotesNormalizer())
            ->add(new TypoNormalizer());

        $this->modelBuilder = new ModelBuilder($normalizerBag);
    }


    /**
     *
     */
    public function testConceptExpansion()
    {
        $concepts = [
            '~je' => [
                'moi',
                'je',
                'ma personne',
                'bibi'
            ],
            '~vouloir' => [
                'vouloir',
                'veux',
                'envie'
            ],
            '~manger' => [
                'manger',
                'grailler',
                'bouffer'
            ]
        ];

        $model = $this->modelBuilder->build(
            [
                'manger' => [
                    '~je ~vouloir ~manger'
                ]
            ],
            [
                '~je' => [
                    'moi',
                    'je',
                    'ma personne',
                    'bibi'
                ],
                '~vouloir' => [
                    'vouloir',
                    'veux',
                    'envie'
                ],
                '~manger' => [
                    'manger',
                    'grailler',
                    'bouffer'
                ]
            ]
        );

        $this->assertIsArray($model);
        $this->assertArrayHasKey('manger', $model);

        $expected = array_product(
            array_map(
                static function ($phrases) {
                    return count($phrases);
                },
                $concepts
            )
        );

        $this->assertEquals($expected, count($model['manger']));
    }
}
