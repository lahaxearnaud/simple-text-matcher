<?php


namespace alahaxe\SimpleTextMatcher\Tests\Classifiers;


use alahaxe\SimpleTextMatcher\Classifiers\LevenshteinClassifier;
use alahaxe\SimpleTextMatcher\Stemmer;

class LevenshteinClassifierTest extends AbstractClassifierTest
{
    protected function setUp():void
    {
        parent::setUp();
        $this->classifier = new LevenshteinClassifier(new Stemmer());
        $this->classifier->prepareModel(self::TRAINING_DATA);
    }

}
