<?php

namespace alahaxe\SimpleTextMatcher\Normalizers;

/**
 * Class NormalizerBag
 *
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
     *
     * @return NormalizerInterface[]
     *
     * @psalm-return list<NormalizerInterface>
     */
    public function all(): array
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
     *
     * @psalm-return list<NormalizerInterface>
     */
    public function getOrderedByPriority() :array
    {
        usort(
            $this->normalizers,
            static function (NormalizerInterface $a, NormalizerInterface $b) {
                return $a->getPriority() > $b->getPriority();
            }
        );

        return array_values($this->normalizers);
    }

    /**
     * @param NormalizerInterface $classifier
     *
     * @return self
     */
    public function add(NormalizerInterface $classifier) :self
    {
        $this->normalizers[] = $classifier;

        return $this;
    }
}
