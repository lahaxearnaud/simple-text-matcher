<?php

namespace Alahaxe\SimpleTextMatcher\Normalizers;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class NormalizerBag
 *
 * @package Alahaxe\SimpleTextMatcher\Classifiers
 *
 * @template-extends ArrayCollection<int, NormalizerInterface>
 */
class NormalizersBag extends ArrayCollection
{

    /**
     * @inheritDoc
     *
     * @return NormalizerInterface[]
     *
     * @psalm-return list<NormalizerInterface>
     */
    public function all(): array
    {
        return $this->toArray();
    }

    /**
     * @param array|NormalizerInterface $items
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
     * @return NormalizerInterface[]
     *
     * @psalm-return list<NormalizerInterface>
     */
    public function getOrderedByPriority() :array
    {
        $elements = $this->toArray();

        usort(
            $elements,
            static function (NormalizerInterface $normalizerA, NormalizerInterface $normalizerB) {
                return $normalizerA->getPriority() > $normalizerB->getPriority();
            }
        );

        return array_values($elements);
    }

    /**
     * @param string $query
     *
     * @return string
     */
    public function apply(string $query):string
    {
        foreach ($this->getOrderedByPriority() as $normalizer) {
            $query = $normalizer->normalize($query);
        }

        return $query;
    }
}
