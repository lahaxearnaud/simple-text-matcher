<?php

namespace alahaxe\SimpleTextMatcher\Entities;

/**
 * Class AbstractRegexExtractor
 * @package alahaxe\SimpleTextMatcher\Entities
 */
abstract class AbstractRegexExtractor implements EntityExtractorInterface
{

    /**
     * @return array
     */
    public abstract function getRegexes():array;

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
     * @return Entity[]
     */
    public function extract(string $question): array
    {
        $result = [];
        foreach ($this->getRegexes() as $regex) {
            preg_match_all($regex, $question, $matches);
            $result = array_merge($result, array_map(function ($value) {
                return new Entity($this->getTypeExtracted(), $this->normalizeValue($value));
            }, $matches[0]));
        }

        return $result;
    }
}
