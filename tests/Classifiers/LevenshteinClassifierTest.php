<?php


namespace Alahaxe\SimpleTextMatcher\Tests\Classifiers;

use Alahaxe\SimpleTextMatcher\Classifiers\LevenshteinClassifier;
use Alahaxe\SimpleTextMatcher\Stemmer;

class LevenshteinClassifierTest extends AbstractClassifierTest
{
    protected function setUp():void
    {
        parent::setUp();
        $this->classifier = new LevenshteinClassifier(new Stemmer());
        $this->classifier->prepareModel(self::TRAINING_DATA);
    }
}
