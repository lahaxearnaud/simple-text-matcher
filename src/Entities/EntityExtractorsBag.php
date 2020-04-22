<?php

namespace Alahaxe\SimpleTextMatcher\Entities;

/**
 * Class EntityExtractorsBag
 *
 */
class EntityExtractorsBag implements \Countable, \ArrayAccess
{
    /**
     * @var EntityExtractorInterface[]
     */
    protected $extractors = [];

    /**
     * @inheritDoc
     */
    public function count()
    {
        return \count($this->extractors);
    }

    /**
     * @inheritDoc
     * @return     EntityExtractorInterface[]
     */
    public function all()
    {
        return array_values($this->extractors);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->extractors[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->extractors[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->extractors[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        if (isset($this->extractors[$offset])) {
            unset($this->extractors[$offset]);
        }
    }

    /**
     * @param EntityExtractorInterface $extractor
     * @return $this
     */
    public function add(EntityExtractorInterface $extractor) :self
    {
        $this->extractors[] = $extractor;

        return $this;
    }

    /**
     * @param string $question
     *
     * @return EntityBag
     */
    public function apply(string $question):EntityBag
    {
        $result = new EntityBag();
        foreach ($this->extractors as $extractor) {
            $result->merge($extractor->extract($question));
        }

        return $result;
    }
}
