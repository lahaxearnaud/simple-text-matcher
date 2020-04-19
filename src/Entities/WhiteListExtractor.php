<?php

namespace alahaxe\SimpleTextMatcher\Entities;

/**
 * Class WhiteListExtractor
 * @package alahaxe\SimpleTextMatcher\Entities
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
     * WhiteListExtractor constructor.
     * @param string $type
     * @param string[] $possibleValues
     */
    public function __construct(string $type, array $possibleValues)
    {
        $this->type = $type;
        foreach ($possibleValues as $possibleValue) {
            $this->possibleValues[mb_strtolower($possibleValue)] = $possibleValue;
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
        $words = explode(' ', mb_strtolower($question));
        foreach ($words as $word) {
            if (isset($this->possibleValues[$word])) {
                $results->add(new Entity($this->getTypeExtracted(), $this->possibleValues[$word]));
            }
        }

        return $results;
    }
}
