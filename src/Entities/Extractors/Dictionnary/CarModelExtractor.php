<?php

namespace Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary;

/**
 *
 * @package Alahaxe\SimpleTextMatcher\Entities
 */
class CarModelExtractor extends FileDictionnaryExtractor
{
    /**
     * CarBrandExtractor constructor.
     *
     * @param string|null $dataFilePath
     */
    public function __construct(string $dataFilePath = null)
    {
        $dataFilePath = $dataFilePath ?? __DIR__ . '/../../../../Resources/dataset/car_models.txt';

        parent::__construct('CAR_MODEL', $dataFilePath);
    }
}
