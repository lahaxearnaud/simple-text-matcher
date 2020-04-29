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

$model = require(__DIR__.'/tests/model.php');

$engine->prepare($model['training'], $model['synonyms'], $model['intentExtractors']);
echo 'Memory: '.(number_format(memory_get_usage()/(1024*1024), 2)).'Mb'.PHP_EOL;

while (($question = readline("Question : ")) !== '') {
    $message = new \Alahaxe\SimpleTextMatcher\Message($question);
    $engine->predict($message);
    echo 'Normalized: ' . $message->getNormalizedMessage() . PHP_EOL;
    echo 'Flags: ' . json_encode($message->getFlags()) . PHP_EOL;
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


    echo 'Performance: '.var_export($message->jsonSerialize()['performance'], true).PHP_EOL;
    echo 'Memory: '.(number_format(memory_get_usage()/(1024*1024), 2)).'Mb'.PHP_EOL;
    echo "==========" . PHP_EOL;
}
