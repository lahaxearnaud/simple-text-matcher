<?php

namespace alahaxe\SimpleTextMatcher\Normalizers;

/**
 * Class NormalizerInterface
 *
 * @package alahaxe\SimpleTextMatcher\Normalizer
 */
interface NormalizerInterface
{
    /**
     * @param string $rawText
     *
     * @return string
     */
    public function normalize(string $rawText):string;

    /**
     * Priority the biggest will be the first to be applied
     *
     * @return int
     */
    public function getPriority():int;
}
