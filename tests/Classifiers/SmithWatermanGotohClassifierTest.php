<?php


namespace alahaxe\SimpleTextMatcher\Tests\Classifiers;

use alahaxe\SimpleTextMatcher\Classifiers\SmithWatermanGotohClassifier;
use alahaxe\SimpleTextMatcher\Stemmer;

class SmithWatermanGotohClassifierTest extends AbstractClassifierTest
{
    protected function setUp():void
    {
        parent::setUp();
        $this->classifier = new SmithWatermanGotohClassifier(new Stemmer());
        $this->classifier->prepareModel(self::TRAINING_DATA);
    }
}
