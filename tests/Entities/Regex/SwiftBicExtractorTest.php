<?php

namespace Alahaxe\SimpleTextMatcher\Tests\Entities;

use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorInterface;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\IbanExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\SwiftBicExtractor;
use PHPUnit\Framework\TestCase;

/**
 * Class SwiftBicExtractorTest
 *
 * @package Alahaxe\SimpleTextMatcher\Tests\Entity
 */
class SwiftBicExtractorTest extends TestCase
{

    /**
     * @var EntityExtractorInterface
     */
    protected $extractor;

    protected function setUp():void
    {
        parent::setUp();
        $this->extractor = new SwiftBicExtractor();
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
        $item = 'BNPAFRPPXXX';
        $result = $this->extractor->extract('Mon bic est '.$item);
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
        $item = 'BARC GB 22 XXX';
        $item2 = 'DEUT-DE-FF-XXX';
        $result = $this->extractor->extract('je vais à '.$item.' et de ma femme à '.$item2);

        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals('BARCGB22XXX', $result[0]->getValue());
        $this->assertEquals($this->extractor->getTypeExtracted(), $result[0]->getType());

        $this->assertInstanceOf(Entity::class, $result[1]);
        $this->assertEquals('DEUTDEFFXXX', $result[1]->getValue());
        $this->assertEquals($this->extractor->getTypeExtracted(), $result[1]->getType());
    }
}
