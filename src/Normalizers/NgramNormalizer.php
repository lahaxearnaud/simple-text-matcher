<?php


namespace Alahaxe\SimpleTextMatcher\Normalizers;


/**
 * Class NgramGenerator
 *
 * @package Alahaxe\SimpleTextMatcher
 */
class NgramNormalizer implements NormalizerInterface
{
    /**
     * @var int
     */
    protected $n;

    /**
     * @var int
     */
    protected $minWordLength;

    /**
     * NgramNormalizer constructor.
     * @param int $n
     */
    public function __construct(int $n = 3)
    {
        $this->n = $n;
    }

    /**
     * @param string $rawText
     *
     * @return string
     */
    public function normalize(string $rawText): string
    {
        $words = explode(' ', $rawText);

        $nbWords = count($words);

        $ngram = [];
        for ($i = 0; $i + $this->n <= $nbWords; $i++) {
            $string = "";
            for ($j = 0; $j < $this->n; $j++) {
                $string .= $words[$j + $i];
            }
            $ngram[$i] = $string;
        }

        return implode(' ', $ngram);
    }

    /**
     * Priority the biggest will be the first to be applied
     *
     * @return int
     */
    public function getPriority(): int
    {
        return 0;
    }
}
