<?php

namespace Alahaxe\SimpleTextMatcher\Classifiers;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ClassifiersBag
 *
 * @package Alahaxe\SimpleTextMatcher\Classifiers
 *
 * @template-extends ArrayCollection<int, ClassifierInterface>
 */
class ClassifiersBag extends ArrayCollection
{

    /**
     * @inheritDoc
     * @return     ClassifierInterface[]
     */
    public function all()
    {
        return $this->toArray();
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
                $this->toArray(),
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
    public function add($classifier) :self
    {
        parent::add($classifier);

        return $this;
    }
}
