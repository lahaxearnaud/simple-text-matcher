<?php


namespace alahaxe\SimpleTextMatcher\Tests\Classifiers;

use alahaxe\SimpleTextMatcher\Classifiers\ClassifiersBag;
use alahaxe\SimpleTextMatcher\Classifiers\TrainedRegexClassifier;
use alahaxe\SimpleTextMatcher\Stemmer;
use PHPUnit\Framework\TestCase;

class ClassifiersBagTest extends TestCase
{

    public function testCount()
    {
        $bag = new ClassifiersBag();
        $this->assertEquals(0, $bag->count());
        $this->assertIsArray($bag->all());
        $this->assertCount(0, $bag->all());

        $bag[] = new TrainedRegexClassifier(new Stemmer());
        $this->assertEquals(1, $bag->count());
        $this->assertCount(1, $bag->all());
    }

    public function testAdd()
    {
        $bag = new ClassifiersBag();
        $this->assertEquals(0, $bag->count());
        $bag->add(new TrainedRegexClassifier(new Stemmer()));
        $this->assertEquals(1, $bag->count());
        $this->assertInstanceOf(TrainedRegexClassifier::class, $bag->all()[0]);
    }

    public function testArrayAccess()
    {
        $bag = new ClassifiersBag();
        $this->assertEquals(0, $bag->count());
        $bag['11'] = new TrainedRegexClassifier(new Stemmer());
        $this->assertTrue(isset($bag[11]));
        $this->assertFalse(isset($bag[666]));
        $this->assertInstanceOf(TrainedRegexClassifier::class, $bag['11']);
        unset($bag['11']);
        $this->assertFalse(isset($bag[11]));
    }
}
