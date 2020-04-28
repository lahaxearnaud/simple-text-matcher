<?php

namespace Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary;

/**
 *
 * @package Alahaxe\SimpleTextMatcher\Entities
 */
class InsultExtractor extends FileDictionnaryExtractor
{
    /**
     * InsultExtractor constructor.
     * @param string $lang
     */
    public function __construct(string $lang = 'fr')
    {
        $dataFilePath = $dataFilePath ?? __DIR__ . '/../../../../Resources/dataset/'.$lang.'/insults.txt';

        parent::__construct('INSULT', $dataFilePath);
    }
}
