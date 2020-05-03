<?php

return [
    'name' => 'acheter_voiture',
    'extractors' => [
        \Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\CarBrandExtractor::class,
        \Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\CarModelExtractor::class,
        \Alahaxe\SimpleTextMatcher\Entities\Extractors\Whitelist\CurrencyExtractor::class,
        \Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\NumberExtractor::class
    ],
    'handler' => [
        'handlerClosure' => function (\Alahaxe\SimpleTextMatcher\Message $message) {
            $carBrands = $message->getEntities()->filter(static function (\Alahaxe\SimpleTextMatcher\Entities\Entity $entity) {
                return $entity->getType() === 'CAR_BRAND';
            })->map(static function (\Alahaxe\SimpleTextMatcher\Entities\Entity $entity) {
                return $entity->getValue();
            })->toArray();
            $carModels = $message->getEntities()->filter(static function (\Alahaxe\SimpleTextMatcher\Entities\Entity $entity) {
                return $entity->getType() === 'CAR_MODEL';
            })->map(static function (\Alahaxe\SimpleTextMatcher\Entities\Entity $entity) {
                return $entity->getValue();
            })->toArray();
            $numbers = $message->getEntities()->filter(static function (\Alahaxe\SimpleTextMatcher\Entities\Entity $entity) {
                return $entity->getType() === 'NUMBER';
            })->map(static function (\Alahaxe\SimpleTextMatcher\Entities\Entity $entity) {
                return $entity->getValue();
            })->toArray();
            $currencies = $message->getEntities()->filter(static function (\Alahaxe\SimpleTextMatcher\Entities\Entity $entity) {
                return $entity->getType() === 'CURRENCY';
            })->map(static function (\Alahaxe\SimpleTextMatcher\Entities\Entity $entity) {
                return $entity->getValue();
            })->toArray();

            $responses = [];

            if (empty($carBrands) && empty($carModels)) {
                $responses[] = 'Ha cool, je ne savais pas que tu savais conduire.';
            } elseif (!empty($carBrands) && !empty($carModels)) {
                $responses[] =  sprintf('La %s de chez %s.', current($carModels), current($carBrands));
                $responses[] = 'Je ne m\'y connais pas trop en voiture, mais si tu penses que c\'est un bon choix...';
            } elseif (!empty($carBrands)) {
                $responses[] =  sprintf('Ha oui je connais la marque de voiture %s.', current($carBrands));
                $responses[] = 'Il me semble que ce sont de bonnes voitures.';
            } elseif (!empty($carModels)) {
                $responses[] =  sprintf('Ha oui je connais le model %s.', current($carModels));
                $responses[] = 'Il me semble que c\'est un bon model de voiture.';
            }

            if (!empty($numbers) && !empty($currencies)) {
                $responses[] =  sprintf('%s %s est un prix correct.', current($numbers), current($currencies));
            }

            $message->setResponses($responses);
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
