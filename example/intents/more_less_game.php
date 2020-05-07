<?php

use Alahaxe\SimpleTextMatcher\Conversation\ConversationHelper;
use Alahaxe\SimpleTextMatcher\Message;

return [
    'name' => 'game',
    'extractors' => [],
    'handler' => [
        'handlerClosure' => function (Message $message) {
            $responses = [];
            $message->setExpectAnswer(true);
            $goodAnswer = (int)date('H');

            $helper = new ConversationHelper($message);

            if ($helper->askInt('value', 'Devine un nombre entre 0 et 24. La réponse est:'.$goodAnswer)) {
                $userProposition = $message->getEntities()->getByName('value')->first()->getValue();
                $responses[] = 'Tu as proposé: ' . $userProposition;
                if ($userProposition === $goodAnswer) {
                    $responses[] = 'Gagné !';
                    $helper->reset();
                } elseif ($userProposition > $goodAnswer) {
                    $message->getEntities()->clear();
                    $responses[] = 'C\'est moins !';
                } else {
                    $message->getEntities()->clear();
                    $responses[] = 'C\'est plus !';
                }

                $message->setResponses($responses);
            }
        }
    ],
    'training' => [
        '~je veux jouer',
        '~je veux faire un jeu',
        '~je  souhaite m\'amuser',
    ]
];
