<?php

namespace Alahaxe\SimpleTextMatcher\Tests;

use Alahaxe\SimpleTextMatcher\ModelBuilder;
use Alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use Alahaxe\SimpleTextMatcher\Normalizers\QuotesNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\StopwordsNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\UnpunctuateNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * Class ModelBuilderTest
 *
 * @package Alahaxe\SimpleTextMatcher\Tests
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
     * @return void
     */
    public function testSynonymsExpansion(): void
    {
        $synonyms = [
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
                $synonyms
            )
        );

        $this->assertEquals($expected, count($model['manger']));
    }

    public function testExpandedGlobalSynonymsExpliciteUsage()
    {
        $this->modelBuilder->setGlobalLanguageSynonyms(json_decode(file_get_contents(__DIR__ . '/../Resources/dataset/fr/synonyms.json'), true));
        $models = $this->modelBuilder->build([
            'abandonner' => [
                'je vais ~abandonner'
            ]
        ], []);

        $this->assertIsArray($models['abandonner']);
        // synonyms of abandonner should be applied
        $this->assertGreaterThan(1, count($models['abandonner']));
    }

    public function testExpandedGlobalSynonymsImpliciteUsage()
    {
        $normalizerBag = new NormalizersBag();
        $normalizerBag
            ->add(new LowerCaseNormalizer())
            ->add(new UnaccentNormalizer())
            ->add(new UnpunctuateNormalizer())
            ->add(new QuotesNormalizer())
            ->add(new TypoNormalizer());

        $modelBuilder = new ModelBuilder($normalizerBag, 'fr', true, 5);
        $modelBuilder->setGlobalLanguageSynonyms(json_decode(file_get_contents(__DIR__ . '/../Resources/dataset/fr/synonyms.json'), true));

        $models = $modelBuilder->build([
            'abandonner' => [
                'je vais abandonner'
            ]
        ], []);

        $this->assertIsArray($models['abandonner']);
        // synonyms of abandonner should be applied
        $this->assertGreaterThan(1, count($models['abandonner']));
    }
}
