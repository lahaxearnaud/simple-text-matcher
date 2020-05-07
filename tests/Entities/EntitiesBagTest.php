<?php


namespace Alahaxe\SimpleTextMatcher\Tests\Classifiers;

use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use PHPUnit\Framework\TestCase;

class EntitiesBagTest extends TestCase
{

    public function testCount(): void
    {
        $bag = new EntityBag();
        $this->assertEquals(0, $bag->count());
        $this->assertIsArray($bag->all());
        $this->assertCount(0, $bag->all());

        $bag[] = new Entity('aaa', 'bbb');
        $this->assertEquals(1, $bag->count());
        $this->assertCount(1, $bag->all());
    }

    public function testAdd(): void
    {
        $bag = new EntityBag();
        $this->assertEquals(0, $bag->count());
        $bag->add(new Entity('aaa', 'bbb'));
        $this->assertEquals(1, $bag->count());
        $this->assertInstanceOf(Entity::class, $bag->all()[0]);
    }

    public function testArrayAccess(): void
    {
        $bag = new EntityBag();
        $this->assertEquals(0, $bag->count());
        $bag['11'] = new Entity('aaa', 'bbb');
        $this->assertTrue(isset($bag[11]));
        $this->assertFalse(isset($bag[666]));
        $this->assertInstanceOf(Entity::class, $bag['11']);
        unset($bag['11']);
        $this->assertFalse(isset($bag[11]));
    }

    public function testGetByName():void
    {
        $bag = new EntityBag();
        $bag->add(new Entity('aaa', 'bbb', 'foo'));
        $this->assertEquals('bbb', $bag->getByName('foo')->first()->getValue());
    }

    public function testReplace():void
    {
        $bag = new EntityBag();
        $bag->add(new Entity('aaa', 'bbb', 'foo'));
        $bag->replace(new Entity('aaa', 'ccc', 'foo'));
        $this->assertEquals('ccc', $bag->getByName('foo')->first()->getValue());
    }

    public function testRemove():void
    {
        $bag = new EntityBag();
        $entity = new Entity('aaa', 'bbb', 'foo');
        $bag->add($entity);
        $bag->removeEntity($entity);
        $this->assertEquals(0, $bag->count());
    }
}
