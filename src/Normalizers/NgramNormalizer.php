<?php


namespace Alahaxe\SimpleTextMatcher\Normalizers;


use Alahaxe\SimpleTextMatcher\StringUtils;

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
    protected $sizeOfNGram;

    /**
     * @var int
     */
    protected $minWordLength;

    /**
     * NgramNormalizer constructor.
     * @param int $sizeOfNGram
     */
    public function __construct(int $sizeOfNGram = 3)
    {
        $this->sizeOfNGram = $sizeOfNGram;
    }

    /**
     * @param string $rawText
     *
     * @return string
     */
    public function normalize(string $rawText): string
    {
        $words = StringUtils::words($rawText);

        $nbWords = count($words);

        $ngram = [];
        for ($i = 0; $i + $this->sizeOfNGram <= $nbWords; $i++) {
            $string = "";
            for ($j = 0; $j < $this->sizeOfNGram; $j++) {
                $string .= $words[$j + $i];
            }
            $ngram[$i] = $string;
        }

        return StringUtils::sentence($ngram);
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
