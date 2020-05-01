<?php

namespace Alahaxe\SimpleTextMatcher\Tests\Classifiers;

use Alahaxe\SimpleTextMatcher\Classifiers\NgramNaiveBayesClassifier;
use Alahaxe\SimpleTextMatcher\ModelBuilder;
use Alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use Alahaxe\SimpleTextMatcher\Normalizers\QuotesNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\UnpunctuateNormalizer;
use Alahaxe\SimpleTextMatcher\Stemmer;

/**
 * Class NaiveBayesClassifierTest
 *
 * @package Alahaxe\SimpleTextMatcher\Tests\Classifiers
 */
class NgramNaiveBayesClassifierTest extends NaiveBayesClassifierTest
{

    protected static $CLASSIFIER_CACHE;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        if (self::$CLASSIFIER_CACHE !== null) {
            $this->classifier = self::$CLASSIFIER_CACHE;
            return;
        }

        $stemmer = new Stemmer();
        $normalizerBag = new NormalizersBag();
        $normalizerBag
            ->add(new LowerCaseNormalizer())
            ->add(new UnaccentNormalizer())
            ->add(new UnpunctuateNormalizer())
            ->add(new QuotesNormalizer());

        $modelBuilder = new ModelBuilder($normalizerBag);
        $model = require(__DIR__.'/../model.php');

        self::$CLASSIFIER_CACHE = $this->classifier = new NgramNaiveBayesClassifier($stemmer);
        $this->classifier->prepareModel($modelBuilder->build($model['training'], $model['synonyms']));
    }

    /**
     * @return (mixed|string[])[]
     *
     * @psalm-return array<array-key, array{0: string, 1: string}|mixed>
     */
    public function matchProvider()
    {
        return [
            // perfect match
            ['je dormir chez paul', 'dormir_amis'],
            ['je vais dormir chez paul', 'dormir_amis'],
            ['je vais passer la nuit chez raoul', 'dormir_amis'],
            ['je vais acheter une voiture concessionnaire', 'acheter_voiture'],
            ['je veux m acheter la dernière mercedes', 'acheter_voiture'],
            ['je me demande quel est ton nom', 'question_nom'],
        ];
    }
}
