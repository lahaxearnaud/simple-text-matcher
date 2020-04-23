<?php

namespace Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary;

/**
 *
 * @package Alahaxe\SimpleTextMatcher\Entities
 */
class CarBrandExtractor extends FileDictionnaryExtractor
{
    /**
     * CarBrandExtractor constructor.
     *
     * @param string|null $dataFilePath
     */
    public function __construct(string $dataFilePath = null)
    {
        $dataFilePath = $dataFilePath ?? __DIR__ . '/../../../../Resources/dataset/car_brands.txt';

        parent::__construct('CAR_BRAND', $dataFilePath);
    }
}
