<?php

namespace alahaxe\SimpleTextMatcher\Classifiers;

use alahaxe\SimpleTextMatcher\Stemmer;

/**
 * Interface ClassifierInterface
 *
 * @package alahaxe\SimpleTextMatcher\Classifiers
 */
interface ClassifierInterface
{

    /**
     * @param  string $question
     * @return ClassificationResultsBag
     */
    public function classify(string $question):ClassificationResultsBag;

    /**
     * @return Stemmer
     */
    public function getStemmer():Stemmer;

    /**
     * @param  Stemmer $stemmer
     * @return ClassifierInterface
     */
    public function setStemmer(Stemmer $stemmer):ClassifierInterface;
}
