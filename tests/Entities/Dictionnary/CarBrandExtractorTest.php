<?php


namespace Alahaxe\SimpleTextMatcher\Tests\Entities;

use Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\CarBrandExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\FirstNameExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class CarBrandExtractorTest
 * @package Alahaxe\SimpleTextMatcher\Tests\Entity
 */
class CarBrandExtractorTest extends TestCase
{

    /**
     * @var EntityExtractorInterface
     */
    protected $extractor;

    protected function setUp():void
    {
        parent::setUp();
        $this->extractor = new CarBrandExtractor();
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
        $item = 'ferrari';
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
        $item = 'suzuki';
        $item2 = 'SAAB';
        $result = $this->extractor->extract('Mon email est '.$item.' et celle de ma femme est '.$item2);

        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($item, $result[0]->getValue());
        $this->assertEquals($this->extractor->getTypeExtracted(), $result[0]->getType());

        $this->assertInstanceOf(Entity::class, $result[1]);
        $this->assertEquals('saab', $result[1]->getValue());
        $this->assertEquals($this->extractor->getTypeExtracted(), $result[1]->getType());
    }
}
