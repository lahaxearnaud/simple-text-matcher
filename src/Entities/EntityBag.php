<?php

namespace Alahaxe\SimpleTextMatcher\Entities;

/**
 * Class EntityBag
 *
 * @package Alahaxe\SimpleTextMatcher\Entities
 */
class EntityBag implements \Countable, \ArrayAccess, \JsonSerializable
{
    /**
     * @var Entity[]
     */
    protected $entities = [];

    /**
     * @inheritDoc
     */
    public function count()
    {
        return \count($this->entities);
    }

    /**
     * @inheritDoc
     * @return     Entity[]
     */
    public function all()
    {
        return array_values($this->entities);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->entities[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->entities[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->entities[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        if (isset($this->entities[$offset])) {
            unset($this->entities[$offset]);
        }
    }

    /**
     * @param EntityBag $entityBag
     */
    public function merge(EntityBag $entityBag)
    {
        $this->entities = array_merge($this->entities, $entityBag->all());
    }

    /**
     * @param $items
     */
    public function add($items):void
    {
        if(!is_array($items)) {
            $items = [$items];
        }

        $this->entities = array_merge($this->entities, $items);
    }

    /**
     * @inheritDoc
     *
     * @return Entity[]|mixed
     */
    public function jsonSerialize()
    {
        return $this->entities;
    }
}
