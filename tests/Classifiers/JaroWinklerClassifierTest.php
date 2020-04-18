<?php


namespace alahaxe\SimpleTextMatcher\Tests\Classifiers;



use alahaxe\SimpleTextMatcher\Classifiers\JaroWinklerClassifier;
use alahaxe\SimpleTextMatcher\Stemmer;

/**
 * Class JaroWinklerClassifierTest
 * @package alahaxe\SimpleTextMatcher\Tests\Classifiers
 */
class JaroWinklerClassifierTest extends AbstractClassifierTest
{
    protected function setUp():void
    {
        parent::setUp();
        $this->classifier = new JaroWinklerClassifier(new Stemmer());
        $this->classifier->prepareModel(self::TRAINING_DATA);
    }
}
