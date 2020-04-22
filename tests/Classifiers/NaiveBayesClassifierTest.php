<?php

namespace Alahaxe\SimpleTextMatcher\Tests\Classifiers;

use Alahaxe\SimpleTextMatcher\Classifiers\NaiveBayesClassifier;
use Alahaxe\SimpleTextMatcher\Stemmer;

/**
 * Class NaiveBayesClassifierTest
 *
 * @package Alahaxe\SimpleTextMatcher\Tests\Classifiers
 */
class NaiveBayesClassifierTest extends AbstractClassifierTest
{
    const TRAINING_DATA = [
        "dormir_dehors" => [
            "dormir a l hotel",
            "je vais dormir dans une auberge",
            "passer la nuit au camping"
        ],
        "dormir_amis" => [
            "avec jean on va dormir chez ses parent",
            "je veux me coucher chez paul",
            "je dormir chez jean",
        ],
        "acheter_voiture" => [
            "je vais chez le concessionnaire",
            "je ai repere une voiture je vais l'acheter",
        ]
    ];

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $stemmer = new Stemmer();
        $this->classifier = new NaiveBayesClassifier($stemmer);
        $this->classifier->prepareModel(self::TRAINING_DATA);
    }

    /**
     * @return (mixed|string[])[]
     *
     * @psalm-return array<array-key, array{0: string, 1: string}|mixed>
     */
    public function matchProvider()
    {
        return array_merge(
            parent::matchProvider(),
            [
            // perfect match
            ['dormir a l hotel', 'dormir_dehors'],
            ['je dormir chez paul', 'dormir_amis'],
            ['je vais dormir chez paul', 'dormir_amis'],
            ['je vais acheter une voiture concessionnaire', 'acheter_voiture'],

            // with small alteration
            ['dormirais a l hotel', 'dormir_dehors'],
            ['dormirais a hotel', 'dormir_dehors'],
            ['dormirais hotel', 'dormir_dehors'],

            // with extra spaces
            ['dormirais           hotel', 'dormir_dehors'],

            // with typo
            ['je vais dormire a hotel', 'dormir_dehors'],
            ['je vais dormir a htoel', 'dormir_dehors'],
            ['j\'irais dormir a htoel', 'dormir_dehors'],

            // with extra text
            ['je vais dormir a hotel rue mazagran', 'dormir_dehors'],
            ]
        );
    }
}
