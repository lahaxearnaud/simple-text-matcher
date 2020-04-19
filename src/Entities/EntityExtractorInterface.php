<?php


namespace alahaxe\SimpleTextMatcher\Entities;

/**
 * Interface EntityExtractorInterface
 *
 * @package alahaxe\SimpleTextMatcher\Entities
 */
interface EntityExtractorInterface
{
    /**
     * @param string $question
     *
     * @return Entity[]
     */
    public function extract(string $question):array;

    /**
     * @return string
     */
    public function getTypeExtracted():string;
}
