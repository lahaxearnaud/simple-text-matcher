<?php


namespace Alahaxe\SimpleTextMatcher\Tests\Entities;

use Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\CityExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class CityExtractorTest
 * @package Alahaxe\SimpleTextMatcher\Tests\Entity
 */
class CityExtractorTest extends TestCase
{

    /**
     * @var EntityExtractorInterface
     */
    protected $extractor;

    protected function setUp():void
    {
        parent::setUp();
        $this->extractor = new CityExtractor();
    }

    protected function tearDown():void
    {
        parent::tearDown();

        unset($this->extractor);
    }

    /**
     *
     */
    public function testExtractWithoutItem()
    {
        $result = $this->extractor->extract('coucou');
        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertEmpty($result->all());
    }

    /**
     *
     */
    public function testExtractItem()
    {
        $item = 'nancy';
        $result = $this->extractor->extract('Mon avis  est '.$item);
        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($item, $result[0]->getValue());
        $this->assertEquals($this->extractor->getTypeExtracted(), $result[0]->getType());
    }

    /**
     *
     */
    public function testExtractMultipleItems()
    {
        $item = 'nancy';
        $item2 = 'saint-etienne';
        $result = $this->extractor->extract('je vais à '.$item.' et de ma femme à '.$item2);

        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($item, $result[0]->getValue());
        $this->assertEquals($this->extractor->getTypeExtracted(), $result[0]->getType());

        $this->assertInstanceOf(Entity::class, $result[1]);
        $this->assertEquals($item2, $result[1]->getValue());
        $this->assertEquals($this->extractor->getTypeExtracted(), $result[1]->getType());
    }
}
