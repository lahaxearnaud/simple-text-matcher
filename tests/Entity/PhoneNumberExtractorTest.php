<?php


namespace alahaxe\SimpleTextMatcher\Tests\Entity;


use alahaxe\SimpleTextMatcher\Entities\EmailExtractor;
use alahaxe\SimpleTextMatcher\Entities\Entity;
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
        $this->assertIsArray($result);
        $this->assertEmpty($result);
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
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($result[0]->getValue(), $phoneNumber);
        $this->assertEquals($result[0]->getType(), $this->extractor->getTypeExtracted());
    }
}
