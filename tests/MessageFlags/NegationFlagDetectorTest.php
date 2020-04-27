<?php

namespace alahaxe\SimpleTextMatcher\Tests;

use Alahaxe\SimpleTextMatcher\Message;
use Alahaxe\SimpleTextMatcher\MessageFlags\NegationFlagDetector;
use PHPUnit\Framework\TestCase;

/**
 * Class NegationDetectorTest
 * @package alahaxe\SimpleTextMatcher\Tests
 */
class NegationFlagDetectorTest extends TestCase
{

    /**
     * @var NegationFlagDetector
     */
    protected $negationDetector;

    protected function setUp():void
    {
        parent::setUp();

        $this->negationDetector = new NegationFlagDetector();
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

    /**
     * @param string $question
     * @param bool $result
     *
     * @dataProvider provideQuestions
     */
    public function testNegation(string $question, bool $result)
    {
        $this->assertEquals($result, $this->negationDetector->detect(new Message($question)));
    }
}
