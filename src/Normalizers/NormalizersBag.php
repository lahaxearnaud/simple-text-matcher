<?php

namespace alahaxe\SimpleTextMatcher\Normalizers;

/**
 * Class NormalizerBag
 * @package alahaxe\SimpleTextMatcher\Classifiers
 */
class NormalizersBag implements \Countable, \ArrayAccess
{
    /**
     * @var NormalizerInterface[]
     */
    protected $normalizers = [];

    /**
     * @inheritDoc
     */
    public function count()
    {
        return \count($this->normalizers);
    }

    /**
     * @inheritDoc
     */
    public function all()
    {
        return array_values($this->normalizers);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->normalizers[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->normalizers[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->normalizers[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        if (isset($this->normalizers[$offset])) {
            unset($this->normalizers[$offset]);
        }
    }

    /**
     * @return NormalizerInterface[]
     */
    public function getOrderedByPriority() :array
    {
        usort($this->normalizers, static function (NormalizerInterface $a, NormalizerInterface $b) {
            return $a->getPriority() > $b->getPriority();
        });

        return array_values($this->normalizers);
    }

    /**
     * @param NormalizerInterface $classifier
     * @return NormalizersBag
     */
    public function add(NormalizerInterface $classifier) :NormalizersBag {
        $this->normalizers[] = $classifier;

        return $this;
    }
}
