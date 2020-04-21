<?php

namespace alahaxe\SimpleTextMatcher\Entities;

/**
 *
 * @package alahaxe\SimpleTextMatcher\Entities
 */
class CityExtractor extends FileDictionnaryExtractor
{
    /**
     * FirstNameExtractor constructor.
     * @param string|null $dataFilePath
     */
    public function __construct(string $dataFilePath = null)
    {
        $dataFilePath = $dataFilePath ?? __DIR__.'/../../Resources/dataset/fr/cities.txt';

        parent::__construct('CITY', $dataFilePath);
    }
}
