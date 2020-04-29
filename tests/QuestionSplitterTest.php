<?php


namespace alahaxe\SimpleTextMatcher\Tests;

use Alahaxe\SimpleTextMatcher\Message;
use Alahaxe\SimpleTextMatcher\QuestionSplitter;
use PHPUnit\Framework\TestCase;

/**
 * Class QuestionSplitterTest
 * @package alahaxe\SimpleTextMatcher\Tests
 */
class QuestionSplitterTest extends TestCase
{

    public function questionProvider()
    {
        return [
            [
                'je veux manger des pommes et des bananes',
                1
            ],
            [
                'allume la lumière et eteind la TV',
                2
            ],
            [
                'allume la lumière et  pommes et des bananes',
                2
            ],
            [
                'je vais au resto et après j irais me coucher',
                2
            ],
            [
                'ouvre la porte puis fais sortir le chat',
                2
            ],
            [
                'tomtom et nana vont à la plage',
                1
            ],
            [
                'et',
                1
            ]
        ];
    }

    /**
     * @param $question
     * @param $nbResults
     *
     * @dataProvider questionProvider
     */
    public function testSplit($question, $nbResults)
    {
        $message = new Message($question);
        $results = (new QuestionSplitter())->splitQuestion($message);
        $this->assertIsArray($results);
        $this->assertNotEmpty($results);
        $this->assertCount($nbResults, $results);
    }
}
