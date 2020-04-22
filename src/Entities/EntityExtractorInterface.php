<?php


namespace Alahaxe\SimpleTextMatcher\Entities;

/**
 * Interface EntityExtractorInterface
 *
 * @package Alahaxe\SimpleTextMatcher\Entities
 */
interface EntityExtractorInterface
{
    /**
     * @param string $question
     *
     * @return EntityBag
     */
    public function extract(string $question):EntityBag;

    /**
     * @return string
     */
    public function getTypeExtracted():string;
}
