<?php

namespace Alahaxe\SimpleTextMatcher\Tests\Entities;

use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorInterface;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Whitelist\CountryExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Whitelist\YesNoExtractor;
use PHPUnit\Framework\TestCase;

/**
 * Class YesNoExtractorTest
 * @package Alahaxe\SimpleTextMatcher\Tests\Entity
 */
class YesNoExtractorTest extends TestCase
{

    /**
     * @var EntityExtractorInterface
     */
    protected $extractor;

    protected function setUp():void
    {
        parent::setUp();
        $this->extractor = new YesNoExtractor();
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
        $item = 'oui';
        $result = $this->extractor->extract('Mon avis  est '.$item);
        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals(true, $result[0]->getValue());
        $this->assertEquals($this->extractor->getTypeExtracted(), $result[0]->getType());
    }

    /**
     *
     */
    public function testExtractMultipleItems()
    {
        $item = 'oui';
        $item2 = 'non';
        $result = $this->extractor->extract('Mon email est '.$item.' et celle de ma femme est '.$item2);
        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals(true, $result[0]->getValue());
        $this->assertEquals($this->extractor->getTypeExtracted(), $result[0]->getType());

        $this->assertInstanceOf(Entity::class, $result[1]);
        $this->assertEquals(false, $result[1]->getValue());
        $this->assertEquals($this->extractor->getTypeExtracted(), $result[1]->getType());
    }
}
