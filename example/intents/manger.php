<?php

return [
    'name' => 'manger',
    'extractors' => [
        'city' => \Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\CityExtractor::class,
        'country' => \Alahaxe\SimpleTextMatcher\Entities\Extractors\Whitelist\CountryExtractor::class,
        'zipCode' => \Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\ZipCodeExtractor::class,
    ],
    'handler' => [
        'directAnswer' => [
            'Moi aussi j\'aime beaucoup manger, mais je ne le fais pas souvent.'
        ]
    ],
    'training' => [
        '~je veux ~manger',
        "~je ai faim",
        "donne à ~manger",
        "~je ~vouloir de la nourriture",
        'aller au ~resto',
        '~manger dans une ~resto',
        'il est l\'heure de ~manger',
        "~je a faim, on peux ~manger ?",
        "~manger avec des amis dans un ~resto",
        "aller au ~resto, avec piere pour ~manger un burger",
        "~manger un plat de saison en famille",
        "~je a cuisiné on peux ~manger",
        "à midi on va manger au ~resto",
        "au petit-déjeuner on va bruncher en couple",
        "au diner on va se faire au ~resto",
        "~je vais ~manger chez les parents de jules",
        "~je vais ~manger au ~resto avec les parents de alexi",
        "~je ~mange un ~plat"
    ]
];
