<?php

$synonyms = [
    '~plat' => [
        'bourguignon',
        'steak frites',
        'kebab',
        'nachos',
        'tartiflette',
        'raclette',
        'fondue'
    ],
    '~hotel' => [
        'hotel',
        'hôtel',
        'auberge',
        'camping',
        'auberge de jeunesse',
        'gite',
        'airbnb',
    ],
    '~resto' => [
        'resto',
        'restaurant',
        'auberge',
        'brasserie',
        'bistrot',
        'bistro',
    ],
    '~piece' => [
        'cuisine',
        'chambre',
        'garage',
        'salon',
        'séjour',
    ],
    '~dormir' => [
        'dormir',
        'dodo',
        'pioncer',
        'sieste',
        'me coucher',
        'me reposer',
        'passer la nuit'
    ],
    '~vouloir' => [
        'veux',
        'voudrais'
    ],
    '~parent' => [
        'parent',
        'parents',
        'daron',
        'darons',
        'geniteur',
        'geniteurs',
    ],
    '~amis' => [
        'amis',
        'copain',
        'pote'
    ],
    '~je' => [
        'je',
        'moi'
    ],
    '~voiture' => [
        'voiture',
        'renault',
        'citroen',
        'mercedes',
        'auto',
        'automobile',
        'clio',
        'megane',
    ],
    '~nouveau' => [
        'nouveau',
        'nouvelle',
        'new'
    ],
    '~acheter' => [
        'acheter',
        'payer',
        'dépenser'
    ],
    '~manger' => [
        'manger',
        'consommer',
        'ingurgiter',
        'bouffer',
        'bouffe'
    ]
];

$training = [
    'manger' => [
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
        "~je ~mange un ~plat"
    ],
    'dormir_maison' => [
        '~je ~vouloir ~dormir',
        '~je vais aller au lit',
        '~je ~vouloir aller au lit',
        '~je ~vouloir aller me coucher',
        '~je suis fatigué, ~je vais me coucher',
        'faire ~dormir dans ma ~piece',
        'faire ~dormir à la maison'
    ],
    'dormir_dehors' => [
        '~je ~vouloir ~dormir à l\'~hotel',
        '~je ~dormir au ~hotel',
        '~je ~dormir dans ~piece une ~hotel',
        '~je a ~dormir dans un petit ~hotel à Paris',
        'faire ~dormir à l\'~hotel',
        '~je vais ~dormir à l\'~hotel',
        '~je vais ~dormir dans une ~piece d\'~hotel',
    ],
    'dormir_amis' => [
        '~je ~vouloir ~dormir chez paul',
        '~je vais ~dormir chez paul',
        '~dormir dans la maison d\'un ~amis',
        'avec jean on va ~dormir chez ses ~parent',
        '~dormir chez les ~parent de paul',
        'cette nuit ~je vais ~dormir dans la ~piece d\'amis de Juliette',
        '~je ~vouloir aller ~dormir chez un amis',
        '~je ~vouloir aller ~dormir chez paul',
        'est-ce que ~je peux aller ~dormir chez regis ?',
        'julien et moi on voudrais allez ~dormir chez lui'
    ],
    'acheter_voiture' => [
        '~je ~vouloir ~acheter une ~voiture',
        '~je vais chez le concessionnaire',
        '~je ai repéré une ~voiture, je vais l\'acheter',
        '~je vais ~acheter une ~voiture',
        '~je vais ~acheter une nouvelle ~voiture',
        '~je ~vouloir une ~nouveau ~voiture',
        'ceci est ma ~nouveau ~voiture',
        '~je viens de faire un crédit pour ~acheter une nouvelle ~voiture',
        "qu'est-ce que tu penses de la ~voiture que je viens d' ~acheter ?",
        "je vais vous prendre cette ~voiture"
    ]
];


$intentExtractors = [
    'manger ' => [],
    'dormir_maison' => [],
    'dormir_dehors' => [
        \Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\CityExtractor::class,
        \Alahaxe\SimpleTextMatcher\Entities\Extractors\Whitelist\CountryExtractor::class
    ],
    'dormir_amis' => [
        \Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\FirstNameExtractor::class
    ],
    'acheter_voiture' => [
        \Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\CarBrandExtractor::class,
        \Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\CarModelExtractor::class,
        \Alahaxe\SimpleTextMatcher\Entities\Extractors\Whitelist\CurrencyExtractor::class,
        \Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\NumberExtractor::class
    ],
];

return [
    'training' => $training,
    'synonyms' => $synonyms,
    'intentExtractors' => $intentExtractors
];
