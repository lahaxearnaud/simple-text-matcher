<?php


namespace alahaxe\SimpleTextMatcher\Classifiers;

use Atomescrochus\StringSimilarities\Levenshtein;

/**
 * Class LevenshteinClassifier
 *
 * @package alahaxe\SimpleTextMatcher\Classifiers
 */
class LevenshteinClassifier extends AbstractTextCompareClassifier
{
    /**
     * @param  string $question
     * @param  string $modelPhrase
     * @return float
     */
    protected function executeComparison(string $question, string $modelPhrase): float
    {
        if (empty($question)) {
            return 0;
        }

        // Levenshtein gives distance and not match percentage
        $distance = (new Levenshtein())->compare($question, $modelPhrase);

        if ($distance === 0) {
            return 1;
        }

        return min(1, 1 - ($distance / strlen($question)));
    }

    /**
     * @param array $trainingData
     */
    public function prepareModel(array $trainingData = []): void
    {
        $maxStringLength = 0;
        foreach ($trainingData as $intent => $phrases) {
            $maxStringLength = max($maxStringLength, max(array_map('mb_strlen', $phrases)));
        }

        foreach ($trainingData as $intent => $phrases) {
            $trainingData[$intent] = array_map(
                static function ($phrase) use ($maxStringLength) {
                    return str_pad($phrase, $maxStringLength, ' ');
                },
                $phrases
            );
        }

        parent::prepareModel($trainingData);
    }
}
