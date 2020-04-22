<?php


namespace Alahaxe\SimpleTextMatcher\Tests\Classifiers;

use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorsBag;
use Alahaxe\SimpleTextMatcher\Entities\NumberExtractor;
use PHPUnit\Framework\TestCase;

class ExtractorsBagTest extends TestCase
{

    public function testCount(): void
    {
        $bag = new EntityExtractorsBag();
        $this->assertEquals(0, $bag->count());
        $this->assertIsArray($bag->all());
        $this->assertCount(0, $bag->all());

        $bag[] = new NumberExtractor();
        $this->assertEquals(1, $bag->count());
        $this->assertCount(1, $bag->all());
    }

    public function testAdd(): void
    {
        $bag = new EntityExtractorsBag();
        $this->assertEquals(0, $bag->count());
        $bag->add(new NumberExtractor());
        $this->assertEquals(1, $bag->count());
        $this->assertInstanceOf(NumberExtractor::class, $bag->all()[0]);
    }

    public function testArrayAccess(): void
    {
        $bag = new EntityExtractorsBag();
        $this->assertEquals(0, $bag->count());
        $bag['11'] = new NumberExtractor();
        $this->assertTrue(isset($bag[11]));
        $this->assertFalse(isset($bag[666]));
        $this->assertInstanceOf(NumberExtractor::class, $bag['11']);
        unset($bag['11']);
        $this->assertFalse(isset($bag[11]));
    }

    public function testApply(): void
    {
        $bag = new EntityExtractorsBag();
        $this->assertEquals(0, $bag->count());
        $bag['11'] = new NumberExtractor();

        $result = $bag->apply('je paie 13,87 euros');

        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);

        $json = $result[0]->jsonSerialize();
        $this->assertIsArray($json);
        $this->assertArrayHasKey('type', $json);
        $this->assertArrayHasKey('value', $json);
    }
}
