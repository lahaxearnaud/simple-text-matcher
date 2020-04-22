<?php

namespace Alahaxe\SimpleTextMatcher\Entities\Extractors\Whitelist;

use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorInterface;
use Alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use Alahaxe\SimpleTextMatcher\Normalizers\SingularizeNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;

/**
 * Class WhiteListExtractor
 * @package Alahaxe\SimpleTextMatcher\Entities
 */
class WhiteListExtractor implements EntityExtractorInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string[]
     */
    protected $possibleValues = [];

    /**
     * @var NormalizersBag
     */
    protected $normalizers;

    /**
     * WhiteListExtractor constructor.
     * @param string $type
     * @param string[] $possibleValues
     */
    public function __construct(string $type, array $possibleValues)
    {
        $this->normalizers = new NormalizersBag();
        $this->normalizers
            ->add(new LowerCaseNormalizer())
            ->add(new UnaccentNormalizer())
            ->add(new SingularizeNormalizer())
        ;

        $this->type = $type;

        // if already associative we keep it as it
        if([] === $possibleValues || array_keys($possibleValues) === range(0, count($possibleValues) - 1)) {
            foreach ($possibleValues as $possibleValue) {
                $this->possibleValues[$this->normalizers->apply($possibleValue)] = $possibleValue;
            }
        } else {
            $this->possibleValues = $possibleValues;
        }
    }

    /**
     * @return string
     */
    public function getTypeExtracted(): string
    {
        return $this->type;
    }

    /**
     * @param string $question
     *
     * @return EntityBag
     */
    public function extract(string $question): EntityBag
    {
        $results = new EntityBag();
        $words = explode(' ', $this->normalizers->apply($question));
        foreach ($words as $word) {
            if (isset($this->possibleValues[$word])) {
                $results->add(new Entity($this->getTypeExtracted(), $this->possibleValues[$word]));
            }
        }

        return $results;
    }
}
