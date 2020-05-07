<?php

return [
    'name' => 'dormir_dehors',
    'extractors' => [
        'city' => \Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\CityExtractor::class,
        'country' => \Alahaxe\SimpleTextMatcher\Entities\Extractors\Whitelist\CountryExtractor::class,
        'zipCode' => \Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\ZipCodeExtractor::class
    ],
    'handler' => [
        'directAnswer' => [
            'Tu me diras comment c\'est dés que je serais incarnée j\'irais y passer la nuit.'
        ]
    ],
    'training' => [
        '~je ~vouloir ~dormir à l\'~hotel',
        '~je ~dormir au ~hotel',
        '~je ~dormir dans ~piece une ~hotel',
        '~je a ~dormir dans un petit ~hotel à Paris',
        '~je a ~dormir dans un petit ~hotel qui est situé rue du pape',
        'faire ~dormir à l\'~hotel',
        '~je vais ~dormir à l\'~hotel',
        '~je vais ~dormir dans une ~piece d\'~hotel',
        '~je vais camper dans les vosges',
        '~je vais dormir chez elle cette nuit',
        'pourvu que cette nuit ~je dorme dans une bon ~hotel',
    ]
];
