<?php

namespace Alahaxe\SimpleTextMatcher\Tests\Entities;

use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Whitelist\WhiteListExtractor;
use PHPUnit\Framework\TestCase;

/**
 * Class WhiteListExtractorTest
 * @package Alahaxe\SimpleTextMatcher\Tests\Entities
 */
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
