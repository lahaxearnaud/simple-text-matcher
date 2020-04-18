<?php

require 'vendor/autoload.php';

$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
$dispatcher->addSubscriber(new \alahaxe\SimpleTextMatcher\Subscribers\ModelCacheSubscriber(__DIR__.'/model_cache.json'));
$dispatcher->addSubscriber(new \alahaxe\SimpleTextMatcher\Subscribers\StemmerCacheSubscriber(__DIR__.'/stemmer_cache.json'));

$classifiers = new \alahaxe\SimpleTextMatcher\Classifiers\ClassifiersBag();
$classifiers
    ->add(new \alahaxe\SimpleTextMatcher\Classifiers\TrainedRegexClassifier())
    ->add(new \alahaxe\SimpleTextMatcher\Classifiers\NaiveBayesClassifier())
    ->add(new \alahaxe\SimpleTextMatcher\Classifiers\JaroWinklerClassifier())
    ->add(new \alahaxe\SimpleTextMatcher\Classifiers\LevenshteinClassifier())
    ->add(new \alahaxe\SimpleTextMatcher\Classifiers\SmithWatermanGotohClassifier());


$normalizers = new \alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag();

$normalizers->add(new \alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer())
    ->add(new \alahaxe\SimpleTextMatcher\Normalizers\StopwordsNormalizer())
    ->add(new \alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer())
    ->add(new \alahaxe\SimpleTextMatcher\Normalizers\UnpunctuateNormalizer())
    ->add(new \alahaxe\SimpleTextMatcher\Normalizers\QuotesNormalizer())
    ->add(new \alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer());

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
        '~je ~vouloir acheter une ~voiture',
        '~je vais chez le concessionnaire',
        '~je ai repéré une ~voiture, je vais l\'acheter',
        '~je vais acheter une ~voiture',
        '~je vais acheter une nouvelle ~voiture',
        '~je ~vouloir une ~nouveau ~voiture',
        'ceci est ma ~nouveau ~voiture'
    ]
];

$engine = new \alahaxe\SimpleTextMatcher\Engine(
    $dispatcher,
    new \alahaxe\SimpleTextMatcher\ModelBuilder($normalizers),
    $normalizers,
    $classifiers,
    new \alahaxe\SimpleTextMatcher\Stemmer()
);

$engine->prepare($model, $synonyms);

$questions = [
    'je peux dormir chez les darons de paul ?',
    'je vais me coucher',
    'je veux manger une pomme',
    'jean va dormir avec ses darons',
    'je vais dormir chez un pote',
    'je pense que je vais m\'acheter une voiture verte',
    'je vais acheter une nouvelle voiture',
    'c est ma nouvelle bagnole',
    'kdsjk kdskd dskdk'
];

foreach ($questions as $question) {
    $message = new \alahaxe\SimpleTextMatcher\Message($question);
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

    echo 'Performance: '.var_export($message->jsonSerialize()['performance'], true);
    echo "==========" . PHP_EOL;
}
