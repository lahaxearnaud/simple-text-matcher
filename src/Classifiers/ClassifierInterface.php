<?php

namespace Alahaxe\SimpleTextMatcher\Classifiers;

use Alahaxe\SimpleTextMatcher\Message;
use Alahaxe\SimpleTextMatcher\Stemmer;

/**
 * Interface ClassifierInterface
 *
 * @package Alahaxe\SimpleTextMatcher\Classifiers
 */
interface ClassifierInterface
{

    /**
     * @param  Message $question
     * @return ClassificationResultsBag
     */
    public function classify(Message $question):ClassificationResultsBag;

    /**
     * @param  Stemmer $stemmer
     * @return ClassifierInterface
     */
    public function setStemmer(Stemmer $stemmer):ClassifierInterface;
}
