<?php


namespace alahaxe\SimpleTextMatcher\Tests\Entity;

use alahaxe\SimpleTextMatcher\Entities\Entity;
use alahaxe\SimpleTextMatcher\Entities\EntityExtractorInterface;
use alahaxe\SimpleTextMatcher\Entities\NumberExtractor;
use alahaxe\SimpleTextMatcher\Entities\PercentageExtractor;
use PHPUnit\Framework\TestCase;

/**
 * Class NumberExtractorTest
 * @package alahaxe\SimpleTextMatcher\Tests\Entity
 */
class NumberExtractorTest extends TestCase
{

    /**
     * @var EntityExtractorInterface
     */
    protected $extractor;

    protected function setUp():void
    {
        parent::setUp();
        $this->extractor = new NumberExtractor();
    }

    /**
     *
     */
    public function testExtractWithoutNumber()
    {
        $result = $this->extractor->extract('coucou');
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     *
     */
    public function testExtractNumber()
    {
        $number = '3 210,75';
        $result = $this->extractor->extract('Mon avis  est '.$number);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals(3210.75, $result[0]->getValue());
        $this->assertEquals($this->extractor->getTypeExtracted(), $result[0]->getType());
    }

    /**
     *
     */
    public function testExtractMultipleNumber()
    {
        $number = '10,75';
        $number2 = '20 virgule 66';
        $result = $this->extractor->extract('Mon email est '.$number.' et celle de ma femme est '.$number2);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals(10.75, $result[0]->getValue());
        $this->assertEquals($this->extractor->getTypeExtracted(), $result[0]->getType());

        $this->assertInstanceOf(Entity::class, $result[1]);
        $this->assertEquals(20.66, $result[1]->getValue());
        $this->assertEquals($this->extractor->getTypeExtracted(), $result[1]->getType());
    }
}
