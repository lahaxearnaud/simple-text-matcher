<?php


namespace alahaxe\SimpleTextMatcher\Classifiers;

use Atomescrochus\StringSimilarities\Levenshtein;

/**
 * Class LevenshteinClassifier
 * @package alahaxe\SimpleTextMatcher\Classifiers
 */
class LevenshteinClassifier extends AbstractTextCompareClassifier
{
    /**
     * @param string $question
     * @param string $modelPhrase
     * @return float
     */
    protected function executeComparison(string $question, string $modelPhrase): float
    {
        return (new Levenshtein())->compare($question, $modelPhrase) / 100;
    }
}
