<?php


namespace alahaxe\SimpleTextMatcher\Tests\Entities;


use alahaxe\SimpleTextMatcher\Entities\EmailExtractor;
use alahaxe\SimpleTextMatcher\Entities\Entity;
use alahaxe\SimpleTextMatcher\Entities\EntityBag;
use alahaxe\SimpleTextMatcher\Entities\EntityExtractorInterface;
use alahaxe\SimpleTextMatcher\Entities\PhoneNumberExtractor;
use PHPUnit\Framework\TestCase;

class PhoneNumberExtractorTest extends TestCase
{

    /**
     * @var EntityExtractorInterface
     */
    protected $extractor;

    protected function setUp():void
    {
        parent::setUp();
        $this->extractor = new PhoneNumberExtractor();
    }

    /**
     *
     */
    public function testExtracWithoutPhoneNumber()
    {
        $result = $this->extractor->extract('coucou');
        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertEmpty($result->all());
    }


    /**
     *
     **/
    public function phoneNumberProvider()
    {
        return [
            ['appelez moi au 0612345678', '0612345678'],
            ['appelez moi au +33612345678', '+33612345678'],
            ['appelez moi au +33 6 12 34 56 78', '+33612345678'],
            ['appelez moi au 06.12.34.56.78', '0612345678'],
            ['appelez moi au 0 892 180 180', '0892180180'],
        ];
    }

    /**
     * @dataProvider phoneNumberProvider
     */
    public function testExtractMultipleEmails($question, $phoneNumber)
    {
        $result = $this->extractor->extract($question);
        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($result[0]->getValue(), $phoneNumber);
        $this->assertEquals($result[0]->getType(), $this->extractor->getTypeExtracted());
    }
}
