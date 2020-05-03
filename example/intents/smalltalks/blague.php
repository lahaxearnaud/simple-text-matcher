<?php

return [
    'name' => 'blague',
    'extractors' => [],
    'handler' => [
        'directAnswer' => [
            'Que dit un oignon quand il se cogne ?',
            'Ail'
        ]
    ],
    'training' => [
        '~raconter moi une ~blague',
        '~raconter moi une petite ~blague',
    ]
];
