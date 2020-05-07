<?php

namespace Alahaxe\SimpleTextMatcher\Entities;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class EntityBag
 *
 * @package Alahaxe\SimpleTextMatcher\Entities
 *
 * @template-extends ArrayCollection<int, Entity>
 */
class EntityBag extends ArrayCollection implements \JsonSerializable
{
    /**
     * @inheritDoc
     * @return     Entity[]
     */
    public function all()
    {
        return $this->toArray();
    }

    /**
     * @param EntityBag $entityBag
     *
     * @return $this
     */
    public function merge(EntityBag $entityBag):self
    {
        foreach ($entityBag->toArray() as $entity) {
            $this->add($entity);
        }

        return $this;
    }

    /**
     * @param $items
     *
     * @return $this
     */
    public function add($items):self
    {
        if (!is_array($items)) {
            $items = [$items];
        }

        foreach ($items as $item) {
            parent::add($item);
        }

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @return Entity[]|mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function getByName(string $name):self
    {
        $bag = new EntityBag();
        /** @var Entity $entity */
        foreach ($this->toArray() as $entity) {
            if ($entity->getName() === $name) {
                $bag->add($entity);
            }
        }

        return $bag;
    }

    /**
     * @param Entity $item
     *
     * @return $this
     */
    public function removeEntity(Entity $item):self
    {
        /** @var Entity $entity */
        foreach ($this->toArray() as $key => $entity) {
            if ($entity->getName() === $item->getName()) {
                $this->offsetUnset($key);
                break;
            }
        }

        return $this;
    }

    /**
     * @param Entity $item
     *
     * @return $this
     */
    public function replace(Entity $item):self
    {

        $this->removeEntity($item);
        $this->add($item);

        return $this;
    }
}
