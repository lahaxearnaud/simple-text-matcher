<?php

namespace alahaxe\SimpleTextMatcher;

use alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;

/**
 * Class ModelBuilder
 * @package alahaxe\SimpleTextMatcher
 */
class ModelBuilder
{
    /**
     * @var NormalizersBag
     */
    protected $normalizers;

    /**
     * Trainer constructor.
     * @param NormalizersBag $normalizers
     */
    public function __construct(NormalizersBag $normalizers)
    {
        $this->normalizers = $normalizers;
    }

    /**
     * @param array $training
     * @param array $synonyms
     * @return array
     */
    public function build(array $training, array $synonyms):array
    {
        $training = $this->expandConcepts($training, $synonyms);
        $training = $this->applyNormalizersOnTrainingModel($training);

        return $training;
    }

    /**
     * @param array $training
     *
     * @return array
     */
    protected function applyNormalizersOnTrainingModel(array $training)
    {
        foreach ($training as $intent => $phrases) {
            foreach ($phrases as $index => $phrase) {
                foreach ($this->normalizers->getOrderedByPriority() as $normalizer) {
                    $phrase = $normalizer->normalize($phrase);
                }
                $training[$intent][$index] = $phrase;
            }
        }

        return $training;
    }

    /**
     * @param array $training
     * @param array $synonyms
     *
     * @return array
     */
    protected function expandConcepts(array $training, array $synonyms)
    {
        foreach ($synonyms as $synonym => $replacements) {
            foreach ($training as $intent => &$phrases) {
                foreach ($phrases as $index => $phrase) {
                    if (preg_match('/' . $synonym . '/i', $phrase)) {
                        foreach ($replacements as $replacement) {
                            $phrases[] = str_replace($synonym, $replacement, $phrase);
                        }
                    }
                }
            }

        }

        foreach ($training as $intent => &$phrases) {
            $phrases = array_values(array_unique(array_filter($phrases, static function ($phrase) {
                return strpos($phrase, '~') === false;
            })));
        }

        return $training;
    }
}
