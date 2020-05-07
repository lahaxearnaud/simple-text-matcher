<?php

use Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\CarBrandExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\CarModelExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Whitelist\CurrencyExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\NumberExtractor;

return [
    'name' => 'acheter_voiture',
    'extractors' => [
        'brand' => CarBrandExtractor::class,
        'model' => CarModelExtractor::class,
        'currency' => CurrencyExtractor::class,
        'price' => NumberExtractor::class
    ],
    'handler' => [
        'handlerClosure' => function (\Alahaxe\SimpleTextMatcher\Message $message) {
            $conversationHelper = new \Alahaxe\SimpleTextMatcher\Conversation\ConversationHelper($message);
            if ($conversationHelper->askEntity('brand', 'Quelle marque ?', new CarBrandExtractor())
                && $conversationHelper->askEntity('model', 'Quel model ?', new CarModelExtractor())
                && $conversationHelper->askNumber('price', 'Quel prix ?')
                && $conversationHelper->askEntity('currency', 'Quelle device ?', new CurrencyExtractor())
            ) {
                $carBrand = $message->getEntities()->getByName('brand')->first()->getValue();
                $carModel = $message->getEntities()->getByName('model')->first()->getValue();
                $price = $message->getEntities()->getByName('price')->first()->getValue();
                $currency = $message->getEntities()->getByName('currency')->first()->getValue();

                $question = sprintf(
                    'Vous voulez acheter une %s de la marque %s pour %f%s',
                    $carModel,
                    $carBrand,
                    $price,
                    $currency
                );

                if ($conversationHelper->confirm('confirm', $question)) {
                    $confirm = $message->getEntities()->getByName('confirm')->first()->getValue();
                    $message->setResponses([
                        $confirm? 'OK c\est validé': 'on oublie'
                    ]);
                }

            }

        }
    ],
    'training' => [
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
