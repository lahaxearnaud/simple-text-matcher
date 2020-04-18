<?php

namespace alahaxe\SimpleTextMatcher\Tests\Classifiers;


use alahaxe\SimpleTextMatcher\Classifiers\ClassificationResult;
use alahaxe\SimpleTextMatcher\Classifiers\ClassificationResultsBag;
use PHPUnit\Framework\TestCase;

/**
 * Class ClassificationResultsBagTest
 * @package alahaxe\SimpleTextMatcher\Tests\Classifiers
 */
class ClassificationResultsBagTest extends TestCase
{

    public function testArrayAccess()
    {
        $bag = new ClassificationResultsBag();
        $bag[11] = new ClassificationResult('A', 'a', 0.8, 0);
        $this->assertEquals(1, $bag->count());
        $this->assertTrue(isset($bag[11]));
        $this->assertEquals('A', $bag[11]->getClassifier());

    }

    public function testTopMerge()
    {
        $bag = new ClassificationResultsBag();

        $this->assertEmpty($bag->getTopIntents(5, 0));

        $bag->add([
            new ClassificationResult('A', 'a', 0.8, 0),
            new ClassificationResult('B', 'b', 0.3, 0),
            new ClassificationResult('C', 'c', 0.9, 0),
            new ClassificationResult('D', 'd', 0.2, 0),
        ]);

        $this->assertNotEmpty($bag->getTopIntents(2, 0.5));
        $this->assertEmpty($bag->getTopIntents(2, 1));

        $this->assertEquals(2, $bag->getTopIntents(2, 0.8)->count());
        $this->assertEquals(2, $bag->getTopIntents(2, 0.1)->count());

    }

    public function testMerge()
    {
        $resultA = new ClassificationResult('A', 'a', 1, 0);
        $resultB = new ClassificationResult('B', 'b', 1, 0);

        $bagA = new ClassificationResultsBag();
        $bagA->add($resultA);
        $bagA->add($resultB);

        $bagB = new ClassificationResultsBag();
        $bagB->add($resultA);
        $bagB->add($resultB);

        $this->assertEquals(2, $bagA->count());
        $this->assertEquals(2, $bagB->count());

        $bagA->merge($bagB);

        $this->assertEquals(4, $bagA->count());
        $this->assertEquals(2, $bagB->count());

        $this->assertEquals(4, count($bagA->all()));
    }
}
