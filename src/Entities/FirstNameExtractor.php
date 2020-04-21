<?php

namespace alahaxe\SimpleTextMatcher\Entities;

/**
 * Country list are from https://github.com/umpirsky/country-list/tree/master/data
 *
 * @package alahaxe\SimpleTextMatcher\Entities
 */
class FirstNameExtractor extends FileDictionnaryExtractor
{
    /**
     * FirstNameExtractor constructor.
     * @param string|null $dataFilePath
     */
    public function __construct(string $dataFilePath = null)
    {
        $dataFilePath = $dataFilePath ?? __DIR__.'/../../Resources/dataset/firstnames.txt';

        parent::__construct('FIRSTNAME', $dataFilePath);
    }
}
