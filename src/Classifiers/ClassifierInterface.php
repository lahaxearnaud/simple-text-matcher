<?php

namespace Alahaxe\SimpleTextMatcher\Classifiers;

use Alahaxe\SimpleTextMatcher\Stemmer;

/**
 * Interface ClassifierInterface
 *
 * @package Alahaxe\SimpleTextMatcher\Classifiers
 */
interface ClassifierInterface
{

    /**
     * @param  string $question
     * @return ClassificationResultsBag
     */
    public function classify(string $question):ClassificationResultsBag;

    /**
     * @param  Stemmer $stemmer
     * @return ClassifierInterface
     */
    public function setStemmer(Stemmer $stemmer):ClassifierInterface;
}
