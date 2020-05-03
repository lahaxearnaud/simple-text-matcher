<?php

return [
    'name' => 'dormir_maison',
    'extractors' => [],
    'handler' => [
        'directAnswer' => [
            'Ferme bien la porte car je vais regarder un film.',
            'Dors bien.'
        ]
    ],
    'training' => [
        '~je ~vouloir ~dormir',
        '~je vais aller au lit',
        '~je ~vouloir aller au lit',
        '~je ~vouloir aller me coucher',
        '~je suis fatigué, ~je vais me coucher',
        'faire ~dormir dans ma ~piece',
        'faire ~dormir à la maison'
    ]
];
