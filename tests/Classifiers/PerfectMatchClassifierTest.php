<?php


namespace Alahaxe\SimpleTextMatcher\Tests\Classifiers;

use Alahaxe\SimpleTextMatcher\Classifiers\LevenshteinClassifier;
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
        $this->classifier = new LevenshteinClassifier(new Stemmer());
        $this->classifier->prepareModel(self::TRAINING_DATA);
    }
}
