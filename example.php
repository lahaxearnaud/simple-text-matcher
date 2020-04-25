<?php
require 'vendor/autoload.php';

$factory = new \Alahaxe\SimpleTextMatcher\EngineFactory();
$engine = $factory->build();

$engine->getExtractors()
    ->add(new \Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\NumberExtractor())
    ->add(new \Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\CarBrandExtractor())
    ->add(new \Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\FirstNameExtractor())
    ->add(new \Alahaxe\SimpleTextMatcher\Entities\Extractors\Whitelist\CurrencyExtractor())
;

$engine->getNormalizers()
    ->add(new \Alahaxe\SimpleTextMatcher\Normalizers\ReplaceNormalizer([
        'bagnole' => 'voiture',
        'slt' => 'salut',
    ]))
;

$synonyms = [
    '~hotel' => [
        'hotel',
        'auberge',
        'camping',
        'auberge de jeunesse',
        'gite'
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
    ]
];

$model = [
    'manger' => [
        '~je veux manger',
        "~je ai faim",
        "donne à manger",
        "~je ~vouloir de la nourriture",
        'aller au ~resto'
    ],
    'dormir_maison' => [
        '~je ~vouloir ~dormir',
        '~je ~vouloir aller au lit',
        '~je ~vouloir aller me coucher',
        'faire ~dormir dans ma ~piece',
        'faire ~dormir à la maison',
        '~je vais ~dormir'
    ],
    'dormir_dehors' => [
        '~je ~vouloir ~dormir à l\'~hotel',
        '~dormir au ~hotel',
        '~je ~vouloir aller ~dormir chez un amis',
        'faire ~dormir à l\'~hotel'
    ],
    'dormir_amis' => [
        '~je ~vouloir ~dormir chez paul',
        '~dormir dans la maison d\'un ~amis',
        'avec jean on va ~dormir chez ses ~parent',
        '~dormir chez les ~parent de paul',
    ],
    'acheter_voiture' => [
        '~je ~vouloir ~acheter une ~voiture',
        '~je vais chez le concessionnaire',
        '~je ai repéré une ~voiture, je vais l\'acheter',
        '~je vais ~acheter une ~voiture',
        '~je vais ~acheter une nouvelle ~voiture',
        '~je ~vouloir une ~nouveau ~voiture',
        'ceci est ma ~nouveau ~voiture'
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

$start = microtime(true);

$engine->prepare($model, $synonyms, $intentExtractors);

$questions = [
    'je peux dormir chez les darons de paul ?',
    'je vais me coucher',
    'je veux manger une pomme',
    'jean va dormir avec ses darons',
    'je vais dormir chez un pote',
    'je pense que je vais m\'acheter une voiture verte',
    'je vais acheter une nouvelle voiture',
    'c est ma nouvelle bagnole',
    'je vais me payer un toute nouvelle auto',
    'je vais me payer la dernière nissan à 16000 euros',
    'je vais voir, avec mon PEL, pour me payer la dernière voiture nissan à 16000 euros',
    'kdsjk kdskd dskdk'
];

foreach ($questions as $question) {
    $message = new \Alahaxe\SimpleTextMatcher\Message($question);
    $engine->predict($message);
    echo 'Question: ' . $message->getRawMessage() . PHP_EOL;
    echo 'Normalized: ' . $message->getNormalizedMessage() . PHP_EOL;
    echo 'Intent: ' . $message->getIntentDetected() . PHP_EOL;

    foreach ($message->getClassification()->all() as $classificationResult) {
        echo sprintf(
            "    - %s %s %f en %s", $classificationResult->getIntent(), $classificationResult->getClassifier(),
            $classificationResult->getScore(), $classificationResult->getDuration()
        ) . PHP_EOL;
    }

    echo 'Entities: ' .PHP_EOL;
    foreach ($message->getEntities()->all() as $entity) {
        echo sprintf(
                "    - %s %s ", $entity->getType(), $entity->getValue(),
            ) . PHP_EOL;
    }


    echo 'Performance: '.var_export($message->jsonSerialize()['performance'], true);
    echo "==========" . PHP_EOL;
}


echo  (microtime(true) - $start). PHP_EOL;
