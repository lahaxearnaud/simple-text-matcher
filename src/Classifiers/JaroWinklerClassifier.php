<?php


namespace alahaxe\SimpleTextMatcher\Classifiers;

use Atomescrochus\StringSimilarities\JaroWinkler;

/**
 * Class JaroWinklerClassifier
 *
 * @package alahaxe\SimpleTextMatcher\Classifiers
 */
class JaroWinklerClassifier extends AbstractTextCompareClassifier
{
    /**
     * @param  string $question
     * @param  string $modelPhrase
     * @return float
     */
    protected function executeComparison(string $question, string $modelPhrase): float
    {
        return (new JaroWinkler())->compare($question, $modelPhrase);
    }
}
