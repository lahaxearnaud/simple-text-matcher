<?php

namespace Alahaxe\SimpleTextMatcher\Entities;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class EntityExtractorsBag
 *
 * @template-extends ArrayCollection<int, EntityExtractorInterface>
 */
class EntityExtractorsBag extends ArrayCollection
{

    /**
     * @inheritDoc
     * @return     EntityExtractorInterface[]
     */
    public function all()
    {
        return $this->toArray();
    }

    /**
     * @param EntityExtractorInterface|EntityExtractorInterface[] $items
     *
     * @return $this
     */
    public function add($items) :self
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
     * @param array $types
     *
     * @return EntityExtractorsBag
     */
    public function getByTypes(array $types):self
    {
        $bag = new EntityExtractorsBag();
        foreach ($this->toArray() as $extractor) {
            if (in_array(get_class($extractor), $types, true)) {
                $bag->add($extractor);
            }
        }

        return $bag;
    }

    /**
     * @param string $question
     *
     * @return EntityBag
     */
    public function apply(string $question):EntityBag
    {
        $result = new EntityBag();
        /** @var EntityExtractorInterface $extractor */
        foreach ($this->toArray() as $extractor) {
            $result->merge($extractor->extract($question));
        }

        return $result;
    }
}
