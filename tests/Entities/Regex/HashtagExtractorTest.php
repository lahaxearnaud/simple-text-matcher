<?php

namespace Alahaxe\SimpleTextMatcher\Tests\Entities;

use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorInterface;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\HashtagExtractor;
use PHPUnit\Framework\TestCase;

/**
 * Class HashtagExtractorTest
 * @package Alahaxe\SimpleTextMatcher\Tests\Entities
 */
class HashtagExtractorTest extends TestCase
{

    /**
     * @var EntityExtractorInterface
     */
    protected $extractor;

    protected function setUp():void
    {
        parent::setUp();
        $this->extractor = new HashtagExtractor();
    }

    /**
     *
     */
    public function testExtracWithoutEmail()
    {
        $result = $this->extractor->extract('coucou');
        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertEmpty($result->all());
    }

    /**
     *
     */
    public function testExtractEmail()
    {
        $email = '#chatbot';
        $result = $this->extractor->extract('Mon email est '.$email);
        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($result[0]->getValue(), $email);
        $this->assertEquals($result[0]->getType(), $this->extractor->getTypeExtracted());
    }

    /**
     *
     */
    public function testExtractMultipleEmails()
    {
        $email = '#foo';
        $email2 = '#BAR';
        $result = $this->extractor->extract('Mon email est '.$email.' et celle de ma femme est '.$email2);
        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($result[0]->getValue(), $email);
        $this->assertEquals($result[0]->getType(), $this->extractor->getTypeExtracted());

        $this->assertInstanceOf(Entity::class, $result[1]);
        $this->assertEquals($result[1]->getValue(), $email2);
        $this->assertEquals($result[1]->getType(), $this->extractor->getTypeExtracted());
    }
}
