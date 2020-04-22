<?php

namespace Alahaxe\SimpleTextMatcher\Entities;

/**
 * Country list are from https://github.com/umpirsky/country-list/tree/master/data
 *
 * @package Alahaxe\SimpleTextMatcher\Entities
 */
class CountryExtractor extends WhiteListExtractor
{
    /**
     * CountryExtractor constructor.
     * @param string|null $countryFilePath
     * @param string|null $countryAliasFilePath
     */
    public function __construct(string $countryFilePath = null, string $countryAliasFilePath = null)
    {
        $countryFilePath = $countryFilePath ?? __DIR__.'/../../Resources/dataset/fr/country.php';
        $countryAliasFilePath = $countryAliasFilePath ?? __DIR__.'/../../Resources/dataset/fr/countryAlias.php';

        $countries = [];
        if (file_exists($countryFilePath) && is_readable($countryFilePath)) {
            $countries = array_flip(include $countryFilePath);
        }

        $countriesAliases = [];
        if (file_exists($countryAliasFilePath) && is_readable($countryAliasFilePath)) {
            $countriesAliases = include $countryAliasFilePath;
        }

        foreach ($countriesAliases as $iso2 => $aliases) {
            foreach ($aliases as $alias) {
                $countries[$alias] = $iso2;
            }
        }

        $countries = array_change_key_case($countries, CASE_LOWER);

        parent::__construct('COUNTRY', $countries);
    }
}
