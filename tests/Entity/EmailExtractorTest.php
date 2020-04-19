<?php


namespace alahaxe\SimpleTextMatcher\Tests\Entity;


use alahaxe\SimpleTextMatcher\Entities\EmailExtractor;
use alahaxe\SimpleTextMatcher\Entities\Entity;
use alahaxe\SimpleTextMatcher\Entities\EntityExtractorInterface;
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
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     *
     */
    public function testExtractEmail()
    {
        $email = 'test@test.fr';
        $result = $this->extractor->extract('Mon email est '.$email);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
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
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($result[0]->getValue(), $email);
        $this->assertEquals($result[0]->getType(), $this->extractor->getTypeExtracted());

        $this->assertInstanceOf(Entity::class, $result[1]);
        $this->assertEquals($result[1]->getValue(), $email2);
        $this->assertEquals($result[1]->getType(), $this->extractor->getTypeExtracted());
    }
}
