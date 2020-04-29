<?php

namespace alahaxe\SimpleTextMatcher\Tests\MessageFlags\Detectors;

require_once __DIR__.'/AbstractFlagDetectorTest.php';

use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\NegationFlagDetector;

/**
 * Class NegationDetectorTest
 * @package alahaxe\SimpleTextMatcher\Tests
 */
class NegationFlagDetectorTest extends AbstractFlagDetectorTest
{
    protected function setUp():void
    {
        parent::setUp();

        $this->detector = new NegationFlagDetector();
    }

    public function provideQuestions()
    {
        return [
            [
                'je ne vais pas au cinéma',
                true
            ],
            [
                'je vais au cinéma',
                false
            ],
            [
                'je veux pas manger',
                true
            ],
            [
                'je veux ni fromage ni dessert',
                true
            ],
            [
                'je n\'en veux pas manger',
                true
            ],
            [
                'je ne veux point manger des choux',
                true
            ],
            [
                'le neuneu est passible de prison',
                false
            ],
        ];
    }
}
