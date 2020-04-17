<?php

namespace alahaxe\SimpleTextMatcher\Classifiers;

/**
 * Interface ClassifierInterface
 *
 * @package alahaxe\SimpleTextMatcher\Classifiers
 */
interface ClassifierInterface
{

    /**
     * @param string $question
     * @return ClassificationResultsBag
     */
    public function classify(string $question):ClassificationResultsBag;
}
