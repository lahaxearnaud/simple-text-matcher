<?php


namespace Alahaxe\SimpleTextMatcher\Tests\Classifiers;

use Alahaxe\SimpleTextMatcher\Classifiers\PerfectMatchClassifier;
use Alahaxe\SimpleTextMatcher\Stemmer;

/**
 * Class LevenshteinClassifierTest
 * @package Alahaxe\SimpleTextMatcher\Tests\Classifiers
 */
class PerfectMatchClassifierTest extends AbstractClassifierTest
{
    protected function setUp():void
    {
        parent::setUp();
        $this->classifier = new PerfectMatchClassifier();
        $this->classifier->setStemmer(new Stemmer());
        $this->classifier->prepareModel(self::TRAINING_DATA);
    }

    /**
     * @inheritDoc
     */
    public function matchProvider()
    {
        return [
            // perfect match
            ['je vais chez le concessionnaire', 'acheter_voiture'],
            ['je dormir chez jean', 'dormir_amis'],
            ['passer la nuit au camping', 'dormir_dehors'],
            ['', null]
        ];
    }
}
