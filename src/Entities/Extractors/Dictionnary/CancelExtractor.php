<?php

namespace Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary;

/**
 *
 * @package Alahaxe\SimpleTextMatcher\Entities
 */
class CancelExtractor extends FileDictionnaryExtractor
{
    /**
     * InsultExtractor constructor.
     * @param string $lang
     */
    public function __construct(string $lang = 'fr')
    {
        $dataFilePath = $dataFilePath ?? __DIR__ . '/../../../../Resources/dataset/'.$lang.'/cancel.txt';

        parent::__construct('CANCEL', $dataFilePath);
    }
}
