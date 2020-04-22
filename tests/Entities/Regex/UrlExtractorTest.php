<?php

namespace Alahaxe\SimpleTextMatcher\Tests\Entities;

use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorInterface;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\UrlExtractor;
use PHPUnit\Framework\TestCase;

class UrlExtractorTest extends TestCase
{

    /**
     * @var EntityExtractorInterface
     */
    protected $extractor;

    protected function setUp():void
    {
        parent::setUp();
        $this->extractor = new UrlExtractor();
    }

    /**
     *
     */
    public function testExtracWithoutUrl()
    {
        $result = $this->extractor->extract('coucou');
        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertEmpty($result->all());
        $this->assertEquals(0, $result->count());
    }

    /**
     *
     */
    public function testExtractUrl()
    {
        $url = 'https://coucou.fr';
        $result = $this->extractor->extract('Mon email est '.$url);
        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($result[0]->getValue(), $url);
        $this->assertEquals($result[0]->getType(), $this->extractor->getTypeExtracted());
    }

    /**
     *
     */
    public function testExtractMultipleUrls()
    {
        $url = 'https://coucou.fr';
        $url2 = 'https://pouet.fr';
        $result = $this->extractor->extract('Mon email est '.$url.' et celle de ma femme est '.$url2);
        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($result[0]->getValue(), $url);
        $this->assertEquals($result[0]->getType(), $this->extractor->getTypeExtracted());

        $this->assertInstanceOf(Entity::class, $result[1]);
        $this->assertEquals($result[1]->getValue(), $url2);
        $this->assertEquals($result[1]->getType(), $this->extractor->getTypeExtracted());
    }
}
