<?php

namespace Alahaxe\SimpleTextMatcher\MessageFlags;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Alahaxe\SimpleTextMatcher\Classifiers
 *
 * @template-extends ArrayCollection<int, Flag>
 */
class FlagBag extends ArrayCollection implements \JsonSerializable
{
    /**
     * @inheritDoc
     * @return  Flag[]
     */
    public function all()
    {
        return $this->toArray();
    }

    /**
     * @param $flag
     *
     * @return bool
     */
    public function hasFlag($flag):bool
    {
        if ($flag instanceof Flag) {
            $flag = $flag->getName();
        }

        return $this->filter(static function (Flag $flagItem) use ($flag) {
            return $flagItem->getName() === $flag;
        })->count() > 0;
    }

    /**
     * @param Flag[]|Flag $items
     *
     * @return $this
     */
    public function add($items)
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
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
