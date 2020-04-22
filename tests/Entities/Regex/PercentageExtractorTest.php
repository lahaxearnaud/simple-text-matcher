<?php


namespace Alahaxe\SimpleTextMatcher\Tests\Entities;

use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorInterface;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\PercentageExtractor;
use PHPUnit\Framework\TestCase;

/**
 * Class PercentageExtractorTest
 * @package Alahaxe\SimpleTextMatcher\Tests\Entity
 */
class PercentageExtractorTest extends TestCase
{

    /**
     * @var EntityExtractorInterface
     */
    protected $extractor;

    protected function setUp():void
    {
        parent::setUp();
        $this->extractor = new PercentageExtractor();
    }

    /**
     *
     */
    public function testExtractWithoutPercentage()
    {
        $result = $this->extractor->extract('coucou');
        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertEmpty($result->all());
    }

    /**
     *
     */
    public function testExtractPercentage()
    {
        $percentage = '10,75 pourcent';
        $result = $this->extractor->extract('Mon avis  est '.$percentage);
        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($result[0]->getValue(), 10.75);
        $this->assertEquals($result[0]->getType(), $this->extractor->getTypeExtracted());
    }

    /**
     *
     */
    public function testExtractMultiplePercentage()
    {
        $percentage = '10,75 pourcent';
        $percentage2 = '20 virgule 66 pour cent';
        $result = $this->extractor->extract('Mon email est '.$percentage.' et celle de ma femme est '.$percentage2);
        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($result[0]->getValue(), 10.75);
        $this->assertEquals($result[0]->getType(), $this->extractor->getTypeExtracted());

        $this->assertInstanceOf(Entity::class, $result[1]);
        $this->assertEquals($result[1]->getValue(), 20.66);
        $this->assertEquals($result[1]->getType(), $this->extractor->getTypeExtracted());
    }
}
