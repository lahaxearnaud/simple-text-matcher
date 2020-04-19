<?php


namespace alahaxe\SimpleTextMatcher\Tests\Entity;


use alahaxe\SimpleTextMatcher\Entities\Entity;
use alahaxe\SimpleTextMatcher\Entities\WhiteListExtractor;
use PHPUnit\Framework\TestCase;

class WhiteListExtractorTest extends TestCase
{

    /**
     *
     */
    public function testExtracWithoutData()
    {
        $result = (new WhiteListExtractor('test', [
            'test',
            'fooBar',
            'example'
        ]))->extract('coucou');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     *
     */
    public function testExtracWithoutOneElement()
    {

        $classifer = new WhiteListExtractor('test', [
            'test',
            'fooBar',
            'example'
        ]);
        $result = $classifer->extract('ceci est un Test');

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($result[0]->getValue(), 'test');
        $this->assertEquals($result[0]->getType(), 'test');

        $result = $classifer->extract('ceci est un example');

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($result[0]->getValue(), 'example');
        $this->assertEquals($result[0]->getType(), 'test');
    }

    /**
     *
     */
    public function testExtracWithoutManyElements()
    {

        $classifer = new WhiteListExtractor('test', [
            'test',
            'fooBar',
            'example'
        ]);
        $result = $classifer->extract('ceci est un Test ou un example');

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($result[0]->getValue(), 'test');
        $this->assertEquals($result[0]->getType(), 'test');

        $this->assertInstanceOf(Entity::class, $result[1]);
        $this->assertEquals($result[1]->getValue(), 'example');
        $this->assertEquals($result[1]->getType(), 'test');
    }
}
