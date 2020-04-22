<?php

namespace Alahaxe\SimpleTextMatcher\Entities;

/**
 * Country list are from https://github.com/umpirsky/currency-list/tree/master/data
 *
 * @package Alahaxe\SimpleTextMatcher\Entities
 */
class CurrencyExtractor extends WhiteListExtractor
{
    /**
     * CurrencyExtractor constructor.
     * @param string|null $currencyFilePath
     * @param string|null $currencyAliasFilePath
     */
    public function __construct(string $currencyFilePath = null, string $currencyAliasFilePath = null)
    {
        $currencyFilePath = $currencyFilePath ?? __DIR__.'/../../Resources/dataset/fr/currency.php';
        $currencyAliasFilePath = $currencyAliasFilePath ?? __DIR__.'/../../Resources/dataset/fr/currencyAlias.php';

        $currencies = [];
        if (file_exists($currencyFilePath) && is_readable($currencyFilePath)) {
            $currencies = array_flip(include $currencyFilePath);
        }

        $currenciesAliases = [];
        if (file_exists($currencyAliasFilePath) && is_readable($currencyAliasFilePath)) {
            $currenciesAliases = include $currencyAliasFilePath;
        }

        foreach ($currenciesAliases as $iso2 => $aliases) {
            foreach ($aliases as $alias) {
                $currencies[$alias] = $iso2;
            }
        }

        parent::__construct('CURRENCY', $currencies);
    }
}
