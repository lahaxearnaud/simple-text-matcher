<?php

namespace Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex;

use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorInterface;

/**
 * Class AbstractRegexExtractor
 * @package Alahaxe\SimpleTextMatcher\Entities
 */
abstract class AbstractRegexExtractor implements EntityExtractorInterface
{

    /**
     * @return array
     */
    abstract public function getRegexes():array;

    /**
     * @param string $rawValue
     * @return string
     */
    public function normalizeValue(string $rawValue):string
    {
        return $rawValue;
    }

    /**
     * @param string $question
     *
     * @return EntityBag
     */
    public function extract(string $question): EntityBag
    {
        $result = new EntityBag();
        foreach ($this->getRegexes() as $regex) {
            preg_match_all($regex, $question, $matches);
            $result->add(array_map(function ($value) {
                return new Entity($this->getTypeExtracted(), $this->normalizeValue($value));
            }, $matches[0]));
        }

        return $result;
    }
}
