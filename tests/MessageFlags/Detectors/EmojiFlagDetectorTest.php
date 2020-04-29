<?php

namespace alahaxe\SimpleTextMatcher\Tests\MessageFlags\Detectors;

require_once __DIR__.'/AbstractFlagDetectorTest.php';

use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\EmojiFlagDetector;

/**
 * Class EmojiFlagDetectorTest
 * @package alahaxe\SimpleTextMatcher\Tests
 */
class EmojiFlagDetectorTest extends AbstractFlagDetectorTest
{
    protected function setUp():void
    {
        parent::setUp();

        $this->detector = new EmojiFlagDetector();
    }

    public function provideQuestions()
    {
        return [
            [
                'Quelle est la couleur du cheval blanc d\'henry 4',
                false
            ],
            [
                'je ne vais pas au cinéma :c',
                true
            ],
            [
                'puis-je aller au cinema :D',
                true
            ],
            [
                'je peux toucher, non ? 😊',
                true
            ],
            [
                'can 🤮 give me this',
                true
            ],
            [
                'I am tall 🍆 💦, isn\'t I ?',
                true
            ]
        ];
    }
}
