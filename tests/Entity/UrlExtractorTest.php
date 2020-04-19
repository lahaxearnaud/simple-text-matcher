<?php


namespace alahaxe\SimpleTextMatcher\Tests\Entity;


use alahaxe\SimpleTextMatcher\Entities\Entity;
use alahaxe\SimpleTextMatcher\Entities\EntityExtractorInterface;
use alahaxe\SimpleTextMatcher\Entities\UrlExtractor;
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
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     *
     */
    public function testExtractUrl()
    {
        $url = 'https://coucou.fr';
        $result = $this->extractor->extract('Mon email est '.$url);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
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
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($result[0]->getValue(), $url);
        $this->assertEquals($result[0]->getType(), $this->extractor->getTypeExtracted());

        $this->assertInstanceOf(Entity::class, $result[1]);
        $this->assertEquals($result[1]->getValue(), $url2);
        $this->assertEquals($result[1]->getType(), $this->extractor->getTypeExtracted());
    }
}
