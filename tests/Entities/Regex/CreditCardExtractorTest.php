<?php


namespace Alahaxe\SimpleTextMatcher\Tests\Entities;

use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorInterface;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\CreditCardExtractor;
use PHPUnit\Framework\TestCase;

/**
 * Data from https://www.freeformatter.com/credit-card-number-generator-validator.html
 * Class CreditCardExtractorTest
 * @package Alahaxe\SimpleTextMatcher\Tests\Entity
 */
class CreditCardExtractorTest extends TestCase
{

    /**
     * @var EntityExtractorInterface
     */
    protected $extractor;

    protected function setUp():void
    {
        parent::setUp();
        $this->extractor = new CreditCardExtractor();
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
        $item = '4556 8128 8176 4177'; // visa
        $result = $this->extractor->extract('Mon avis  est '.$item);
        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals(str_replace(' ', '', $item), $result[0]->getValue());
        $this->assertEquals($this->extractor->getTypeExtracted(), $result[0]->getType());
    }

    /**
     *
     */
    public function testExtractMultipleItems()
    {
        $item = '5142 2299 2808 3552'; // MasterCard
        $item2 = '3786 0048 3424 769'; // AMEX
        $result = $this->extractor->extract('je vais à '.$item.' et de ma femme à '.$item2);

        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals(str_replace(' ', '', $item), $result[0]->getValue());
        $this->assertEquals($this->extractor->getTypeExtracted(), $result[0]->getType());

        $this->assertInstanceOf(Entity::class, $result[1]);
        $this->assertEquals(str_replace(' ', '', $item2), $result[1]->getValue());
        $this->assertEquals($this->extractor->getTypeExtracted(), $result[1]->getType());
    }
}
