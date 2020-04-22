<?php


namespace Alahaxe\SimpleTextMatcher\Tests\Classifiers;

use Alahaxe\SimpleTextMatcher\Classifiers\JaroWinklerClassifier;
use Alahaxe\SimpleTextMatcher\Stemmer;

/**
 * Class JaroWinklerClassifierTest
 *
 * @package Alahaxe\SimpleTextMatcher\Tests\Classifiers
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
