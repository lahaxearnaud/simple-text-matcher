<?php


namespace Alahaxe\SimpleTextMatcher\Tests\Entities;


use Alahaxe\SimpleTextMatcher\Entities\EmailExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorInterface;
use PHPUnit\Framework\TestCase;

class EmailExtractorTest extends TestCase
{

    /**
     * @var EntityExtractorInterface
     */
    protected $extractor;

    protected function setUp():void
    {
        parent::setUp();
        $this->extractor = new EmailExtractor();
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
        $email = 'test@test.fr';
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
        $email = 'test@test.fr';
        $email2 = 'test2@test.fr';
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
