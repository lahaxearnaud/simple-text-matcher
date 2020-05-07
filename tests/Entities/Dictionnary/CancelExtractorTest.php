<?php


namespace Alahaxe\SimpleTextMatcher\Tests\Entities;

use Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\CancelExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\CarBrandExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\FirstNameExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorInterface;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\InsultExtractor;
use PHPUnit\Framework\TestCase;

/**
 * Class CancelExtractorTest
 * @package Alahaxe\SimpleTextMatcher\Tests\Entity
 */
class CancelExtractorTest extends TestCase
{

    /**
     * @var EntityExtractorInterface
     */
    protected $extractor;

    protected function setUp():void
    {
        parent::setUp();
        $this->extractor = new CancelExtractor();
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
        $item = 'stop';
        $result = $this->extractor->extract($item);
        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($item, $result[0]->getValue());
        $this->assertEquals($this->extractor->getTypeExtracted(), $result[0]->getType());
    }
}
