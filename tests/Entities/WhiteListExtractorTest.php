<?php


namespace alahaxe\SimpleTextMatcher\Tests\Entities;


use alahaxe\SimpleTextMatcher\Entities\Entity;
use alahaxe\SimpleTextMatcher\Entities\EntityBag;
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

        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertEmpty($result->all());
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

        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($result[0]->getValue(), 'test');
        $this->assertEquals($result[0]->getType(), 'test');

        $result = $classifer->extract('ceci est un example');

        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
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

        $this->assertInstanceOf(EntityBag::class, $result);
        $this->assertNotEmpty($result->all());
        $this->assertInstanceOf(Entity::class, $result[0]);
        $this->assertEquals($result[0]->getValue(), 'test');
        $this->assertEquals($result[0]->getType(), 'test');

        $this->assertInstanceOf(Entity::class, $result[1]);
        $this->assertEquals($result[1]->getValue(), 'example');
        $this->assertEquals($result[1]->getType(), 'test');
    }
}
