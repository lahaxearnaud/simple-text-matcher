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
        if(!is_array($items)) {
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
}
