<?php

namespace alahaxe\SimpleTextMatcher\Tests\MessageFlags\Detectors;

require_once __DIR__.'/AbstractFlagDetectorTest.php';


use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\QuestionFlagDetector;

/**
 * Class NegationDetectorTest
 * @package alahaxe\SimpleTextMatcher\Tests
 */
class QuestionFlagDetectorTest extends AbstractFlagDetectorTest
{
    protected function setUp():void
    {
        parent::setUp();

        $this->detector = new QuestionFlagDetector();
    }

    public function provideQuestions()
    {
        return [
            [
                'Quelle est la couleur du cheval blanc d\'henry 4',
                true
            ],
            [
                'je ne vais pas au cinéma',
                false
            ],
            [
                'puis-je aller au cinema',
                true
            ],
            [
                'je peux toucher, non ?',
                true
            ],
            [
                'can you give me this',
                true
            ],
            [
                'I am tall, isn\'t I ?',
                true
            ]
        ];
    }
}
