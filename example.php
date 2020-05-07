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

$modelFileLoader = new \Alahaxe\SimpleTextMatcher\Loader\FileLoader(__DIR__.'/example');

$engine->prepareWithLoader($modelFileLoader);
echo 'Memory: '.(number_format(memory_get_usage()/(1024*1024), 2)).'Mb'.PHP_EOL;
echo 'Memory Peak: '.(number_format(memory_get_peak_usage()/(1024*1024), 2)).'Mb'.PHP_EOL;

$options = getopt('d');
$debug = isset($options['d']);

$conversationToken = null;
while (($question = readline("Question : ")) !== '') {
    $message = new \Alahaxe\SimpleTextMatcher\Message($question);
    $message->setConversationToken($conversationToken);

    $engine->predict($message, true);

    $conversationToken = $message->getConversationToken();
    if ($debug) {

        /** @var \Alahaxe\SimpleTextMatcher\Message $messageItem */
        foreach (array_merge([$message], $message->getSubMessages()) as $messageItem) {
            echo "------------" . PHP_EOL;
            echo 'Raw: ' . $messageItem->getRawMessage() . PHP_EOL;
            echo 'Normalized: ' . $messageItem->getNormalizedMessage() . PHP_EOL;
            echo 'Flags: ' . json_encode($messageItem->getFlags()) . PHP_EOL;
            echo 'Intent: ' . $messageItem->getIntentDetected() . PHP_EOL;
            echo 'Conversation token: ' . $messageItem->getConversationToken() . PHP_EOL;
            echo 'Expect answer: ' . ($messageItem->isExpectAnswer()?'Y':'N') . PHP_EOL;

            foreach ($messageItem->getClassification()->all() as $classificationResult) {
                echo sprintf(
                        "    - %s %s %f en %s", $classificationResult->getIntent(),
                        $classificationResult->getClassifier(),
                        $classificationResult->getScore(), $classificationResult->getDuration()
                    ) . PHP_EOL;
            }

            echo 'Entities: ' . PHP_EOL;
            foreach ($messageItem->getEntities()->all() as $entity) {
                echo sprintf(
                        "    - %s %s %s ", $entity->getName(), $entity->getType(), $entity->getValue(),
            ) . PHP_EOL;
            }
        }
        echo "------------" . PHP_EOL;
        echo "" . PHP_EOL;
    }

    echo 'Responses: ' .PHP_EOL;
    foreach ($message->getResponses() as $response) {
        echo sprintf(
                "    - %s", json_encode($response),
            ) . PHP_EOL;
    }

    if ($debug) {
        echo 'Performance: ' . var_export($message->jsonSerialize()['performance'], true) . PHP_EOL;
        echo 'Memory: ' . (number_format(memory_get_usage() / (1024 * 1024), 2)) . 'Mb' . PHP_EOL;
    }
    echo "==========" . PHP_EOL;
}
