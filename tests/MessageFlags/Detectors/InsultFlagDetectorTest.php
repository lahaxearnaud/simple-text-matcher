<?php

namespace alahaxe\SimpleTextMatcher\Tests\MessageFlags\Detectors;

require_once __DIR__.'/AbstractFlagDetectorTest.php';

use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\InsultFlagDetector;

/**
 * Class InsultFlagDetectorTest
 * @package alahaxe\SimpleTextMatcher\Tests
 */
class InsultFlagDetectorTest extends AbstractFlagDetectorTest
{

    protected function setUp():void
    {
        parent::setUp();

        $this->detector = new InsultFlagDetector();
    }

    public function provideQuestions()
    {
        return [
            [
                'vous êtes une belle bande de con',
                true
            ],
            [
                'je vais au cinéma',
                false
            ],
            [
                'espèce de sac à foutre',
                true
            ],
            [
                'ta mère la chienne',
                true
            ]
        ];
    }
}
