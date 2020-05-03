<?php

namespace Alahaxe\SimpleTextMatcher;

use Alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;

/**
 * Class ModelBuilder
 *
 * @package Alahaxe\SimpleTextMatcher
 */
class ModelBuilder
{
    /**
     * @var NormalizersBag
     */
    protected $normalizers;

    /**
     * @var bool
     */
    protected $autoExpandGlobalWord = false;

    /**
     * @var int
     */
    protected $minimumSizeForAutoExpand = 5;

    /**
     * @var []
     */
    protected $globalLanguageSynonyms = [];

    /**
     * @var string
     */
    protected $lang = 'fr';

    /**
     * ModelBuilder constructor.
     *
     * @param NormalizersBag|null $normalizers
     * @param string $lang
     * @param bool $autoExpandGlobalWord
     * @param int $minimumSizeForAutoExpand
     */
    public function __construct(NormalizersBag $normalizers = null, string $lang = 'fr', bool $autoExpandGlobalWord = false, int $minimumSizeForAutoExpand = 5)
    {
        $this->normalizers = $normalizers ?? new NormalizersBag();
        $this->autoExpandGlobalWord = $autoExpandGlobalWord;
        $this->minimumSizeForAutoExpand = $minimumSizeForAutoExpand;
        $this->lang = $lang;
    }

    /**
     * @param  array $training
     * @param  array $synonyms
     * @return array
     */
    public function build(array $training, array $synonyms):array
    {
        // user synonyms firsts
        $training = $this->expandSynonyms($training, $synonyms);

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
                $training[$intent][$index] = $this->normalizers->apply($phrase);
            }
        }

        return $training;
    }

    /**
     * @param array $training
     * @param array $synonyms
     * @param string $prefix
     *
     * @return array
     */
    protected function expandSynonyms(array $training, array $synonyms, $prefix = '~')
    {
        $synonyms =  array_merge($this->globalLanguageSynonyms, $synonyms);
        $canAutoExpand = $this->autoExpandGlobalWord;
        foreach ($training as $intent => &$phrases) {
            $potentialSynonyms = $this->extractCandidateToExpand($phrases, $synonyms, $prefix, $canAutoExpand);
            $canAutoExpand = false;

            foreach ($potentialSynonyms as $synonym) {
                $training[$intent] = $this->replaceSynonymsInPhrases($phrases, $synonym, $synonyms, $prefix);
            }
        }

        return $this->removePhrasesWithMissingSynonyms($training, $prefix);
    }

    /**
     * @param array $phrases
     * @param string $synonymWord
     * @param array $synonyms
     * @param string $prefix
     *
     * @return array
     */
    protected function replaceSynonymsInPhrases(array $phrases, string $synonymWord, array $synonyms, string $prefix):array
    {
        foreach ($phrases as $index => $phrase) {
            $needToDeleteCurrent = false;
            $synonymKey = starts_with($synonymWord, $prefix) ? $synonymWord : $prefix.$synonymWord;

            foreach ($synonyms[$synonymKey] as $replacement) {
                if (preg_match('/'.$synonymWord.'/', $phrase)) {
                    $phrases[] = str_replace($synonymWord, $replacement, $phrase);
                    $needToDeleteCurrent = true;
                }
            }

            if ($needToDeleteCurrent) {
                unset($phrases[$index]);
            }
        }

        return $phrases;
    }

    /**
     * @param array $phrases
     * @param array $synonyms
     * @param string $prefix
     * @param bool $canAutoExpand
     * @return array
     */
    public function extractCandidateToExpand(array $phrases, array $synonyms, string $prefix, bool $canAutoExpand)
    {
        $potentialSynonyms = [];
        foreach ($phrases as $phrase) {
            $words = StringUtils::words($phrase);

            foreach ($words as $word) {
                if (starts_with($word, $prefix)) {
                    $potentialSynonyms[] = $word;
                } elseif ($canAutoExpand && strlen($word) > $this->minimumSizeForAutoExpand) {
                    $potentialSynonyms[] = $word;
                }
            }
        }

        $potentialSynonyms = array_filter($potentialSynonyms, static function ($synonym) use ($synonyms, $prefix) {
            $synonymKey = starts_with($synonym, $prefix) ? $synonym : $prefix.$synonym;
            return isset($synonyms[$synonymKey]);
        });

        return $potentialSynonyms;
    }

    /**
     * @param array $training
     * @param string $prefix
     * @return array
     */
    protected function removePhrasesWithMissingSynonyms(array $training, string $prefix)
    {
        foreach ($training as $intent => &$phrases) {
            $phrases = array_values(
                array_unique(
                    array_filter(
                        $phrases,
                        static function ($phrase) use ($prefix) {
                            return strpos($phrase, $prefix) === false;
                        }
                    )
                )
            );
        }

        return $training;
    }

    /**
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }

    /**
     * @param mixed $globalLanguageSynonyms
     */
    public function setGlobalLanguageSynonyms($globalLanguageSynonyms): void
    {
        $this->globalLanguageSynonyms = $globalLanguageSynonyms;
    }

    /**
     * @param NormalizersBag $normalizers
     */
    public function setNormalizers(NormalizersBag $normalizers): void
    {
        $this->normalizers = $normalizers;
    }
}
