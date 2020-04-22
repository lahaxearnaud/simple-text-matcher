<?php


namespace Alahaxe\SimpleTextMatcher\Tests\Classifiers;

use Alahaxe\SimpleTextMatcher\Classifiers\SmithWatermanGotohClassifier;
use Alahaxe\SimpleTextMatcher\Stemmer;

class SmithWatermanGotohClassifierTest extends AbstractClassifierTest
{
    protected function setUp():void
    {
        parent::setUp();
        $this->classifier = new SmithWatermanGotohClassifier(new Stemmer());
        $this->classifier->prepareModel(self::TRAINING_DATA);
    }
}
