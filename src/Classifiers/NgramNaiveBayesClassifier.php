<?php

namespace Alahaxe\SimpleTextMatcher\Classifiers;

use Alahaxe\SimpleTextMatcher\Normalizers\NgramNormalizer;
use Alahaxe\SimpleTextMatcher\Stemmer;

/**
 * Class NgramNaiveBayesClassifier
 * @package Alahaxe\SimpleTextMatcher\Classifiers
 */
class NgramNaiveBayesClassifier extends NaiveBayesClassifier
{
    /**
     * @var NgramNormalizer
     */
    protected $ngramNormalizer;

    /**
     * NgramNaiveBayesClassifier constructor.
     * @param Stemmer|null $stemmer
     * @param NgramNormalizer|null $ngramNormalizer
     */
    public function __construct(Stemmer $stemmer = null, NgramNormalizer $ngramNormalizer = null)
    {
        $this->ngramNormalizer = $ngramNormalizer ?? new NgramNormalizer();

        parent::__construct($stemmer);
    }

    /**
     * @inheritDoc
     */
    protected function prepareSentence(string $sentence): array
    {
        return explode(' ', $this->ngramNormalizer->normalize($this->stemmer->stemPhrase($sentence)));
    }
}
