<?php

require 'vendor/autoload.php';


$data = [
    'je veux manger' => 'manger',
    'j\'ai faim' => 'manger',
    'nous allons manger au restorant' => 'manger',
    'a quelle heure on mange ?' => 'manger',
    "je ai faim" => 'manger',
    "donne à manger" => 'manger',
    "je vouloir de la nourriture" => 'manger',
    'aller au resto' => 'manger',
    'manger dans une resto'=> 'manger',
    'il est l\'heure de manger'=> 'manger',
    "je a faim, on peux manger ?"=> 'manger',
    "manger avec des amis dans un resto"=> 'manger',
    "aller au resto, avec piere pour manger un burger"=> 'manger',
    "manger un plat de saison en famille"=> 'manger',
    "je a cuisiné on peux manger"=> 'manger',
    "à midi on va manger au resto"=> 'manger',
    "au petit-déjeuner on va bruncher en couple"=> 'manger',
    "au diner on va se faire au resto"=> 'manger',
    "je mange un plat"=> 'manger',
    'je vais me coucher' => 'dodo',
    'je vais dormir' => 'dodo',
    'je vais passer la nuit à l hotel' => 'dodo',
    'je vais dormir chez un amis' => 'dodo',
    'je vais aller au lit' => 'dodo',
    'je vouloir aller au lit' => 'dodo',
    'je vouloir aller me coucher' => 'dodo',
    'je suis fatigué, je vais me coucher' => 'dodo',
    'faire dormir dans ma piece' => 'dodo',
    'faire dormir à la maison' => 'dodo',
    'je vouloir dormir à l\'hotel' => 'dodo',
    'je dormir au hotel' => 'dodo',
    'je dormir dans piece une hotel' => 'dodo',
    'je a dormir dans un petit hotel à Paris' => 'dodo',
    'faire dormir à l\'hotel' => 'dodo',
    'je vais dormir à l\'hotel' => 'dodo',
    'je vais dormir dans une piece d\'hotel' => 'dodo',
    'je vouloir dormir chez paul' => 'dodo',
    'je vais dormir chez paul' => 'dodo',
    'dormir dans la maison d\'un amis' => 'dodo',
    'avec jean on va dormir chez ses parent' => 'dodo',
    'dormir chez les parent de paul' => 'dodo',
    'cette nuit je vais dormir dans la piece d\'amis de Juliette' => 'dodo',
    'je vouloir aller dormir chez un amis' => 'dodo',
    'je vouloir aller dormir chez paul' => 'dodo',
    'est-ce que je peux aller dormir chez regis ?' => 'dodo',
    'julien et moi on voudrais allez dormir chez lui' => 'dodo',
];
$dataset = new \Phpml\Dataset\ArrayDataset(array_keys($data), array_values($data));
$split = new \Phpml\CrossValidation\StratifiedRandomSplit($dataset, 0.1);

$transformers = [
    new \Phpml\FeatureExtraction\TokenCountVectorizer($tokenizer = new \Phpml\Tokenization\NGramTokenizer(1, 3), new \Phpml\FeatureExtraction\StopWords\French()),
    new \Phpml\FeatureExtraction\TfIdfTransformer()
];

$pipeline = new \Phpml\Pipeline($transformers, new \Phpml\Classification\SVC(
    Phpml\SupportVectorMachine\Kernel::LINEAR,
    1.0,            // $cost
    3,              // $degree
    null,           // $gamma
    0.0,            // $coef0
    0.001,          // $tolerance
    100,            // $cacheSize
    true,           // $shrinking
    true            // $probabilityEstimates, set to true
));

$start = microtime(true);
$pipeline->train($split->getTrainSamples(), $split->getTrainLabels());
$stop = microtime(true);

$predicted = $pipeline->predict($split->getTestSamples());

echo 'Train: ' . round($stop - $start, 4) . 's'. PHP_EOL;
echo 'Estimator: ' . get_class($pipeline->getEstimator()) . PHP_EOL;
echo 'Tokenizer: ' . get_class($tokenizer) . PHP_EOL;
echo 'Accuracy: ' . \Phpml\Metric\Accuracy::score($split->getTestLabels(), $predicted);

$question  = ['je veux manger au resto avec ma femme'];

/** @var \Phpml\Transformer $transformer */
foreach ($transformers as $transformer) {
    $transformer->transform($question);
}

$result = $pipeline->getEstimator()->predictProbability($question);
var_dump($result);

