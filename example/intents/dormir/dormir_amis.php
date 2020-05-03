<?php

return [
    'name' => 'dormir_amis',
    'extractors' => [
        \Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\FirstNameExtractor::class
    ],
    'handler' => [
        'handlerClosure' => function (\Alahaxe\SimpleTextMatcher\Message $message) {
            /** @var \Alahaxe\SimpleTextMatcher\Entities\Entity[] $nameDetected */
            $nameDetected = $message->getEntities()->filter(static function (\Alahaxe\SimpleTextMatcher\Entities\Entity $entity) {
                return $entity->getType() === 'FIRSTNAME';
            })->map(static function (\Alahaxe\SimpleTextMatcher\Entities\Entity $entity) {
                return $entity->getValue();
            })->toArray();

            $responses = [];
            switch (count($nameDetected)) {
                case 0:
                    $responses[] = 'Cool, je pense que tu vas passer une bonne soirée';
                break;
                default:
                    $responses[] = sprintf('Top ! Tu passeras le bonjours à %s de ma part', implode(' et ', $nameDetected));
            }

            $responses[] = 'Bonne nuit !';

            $message->setResponses($responses);
        }
    ],
    'training' => [
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
    ]
];
