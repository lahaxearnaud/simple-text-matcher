<?php

namespace alahaxe\SimpleTextMatcher\Classifiers;

/**
 * Class ClassifiersBag
 *
 * @package alahaxe\SimpleTextMatcher\Classifiers
 */
class ClassifiersBag implements \Countable, \ArrayAccess
{

    /**
     * @var ClassifierInterface[]
     */
    protected $classifiers = [];

    /**
     * @inheritDoc
     */
    public function count()
    {
        return \count($this->classifiers);
    }

    /**
     * @inheritDoc
     * @return     ClassifierInterface[]
     */
    public function all()
    {
        return array_values($this->classifiers);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->classifiers[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->classifiers[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->classifiers[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        if (isset($this->classifiers[$offset])) {
            unset($this->classifiers[$offset]);
        }
    }

    /**
     * @return ClassifierInterface&TrainingInterface[]
     *
     * @psalm-return list<ClassifierInterface&TrainingInterface>
     */
    public function classifiersWithTraining(): array
    {
        return array_values(
            array_filter(
                $this->classifiers,
                static function (ClassifierInterface $classifier) {
                    return $classifier instanceof TrainingInterface;
                }
            )
        );
    }

    /**
     * @param ClassifierInterface $classifier
     *
     * @return self
     */
    public function add(ClassifierInterface $classifier) :self
    {
        $this->classifiers[] = $classifier;

        return $this;
    }
}
