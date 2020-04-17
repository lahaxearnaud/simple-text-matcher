<?php

namespace alahaxe\SimpleTextMatcher\Classifiers;

use Atomescrochus\StringSimilarities\SmithWatermanGotoh;

/**
 * Class SmithWatermanGotohClassifier
 * @package alahaxe\SimpleTextMatcher\Classifiers
 */
class SmithWatermanGotohClassifier extends AbstractTextCompareClassifier
{
    /**
     * @param string $question
     * @param string $modelPhrase
     * @return float
     */
    protected function executeComparison(string $question, string $modelPhrase): float
    {
        return (new SmithWatermanGotoh())->compare($question, $modelPhrase);
    }
}
