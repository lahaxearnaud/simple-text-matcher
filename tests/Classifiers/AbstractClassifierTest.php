<?php

namespace alahaxe\SimpleTextMatcher\Tests\Classifiers;

use alahaxe\SimpleTextMatcher\Classifiers\ClassifierInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class AbtractTextCompareClassifier
 *
 * @package alahaxe\SimpleTextMatcher\Tests\Classifiers
 */
abstract class AbstractClassifierTest extends TestCase
{
    const TRAINING_DATA = [
        "dormir_dehors" => [
            "dormir a l hotel",
            "passer la nuit au camping"
        ],
        "dormir_amis" => [
            "avec jean on va dormir chez ses parent",
            "je veux me coucher chez paul",
            "je dormir chez jean",
        ],
        "acheter_voiture" => [
            "je vais chez le concessionnaire",
            "je ai repere une voiture je vais l'acheter",
        ]
    ];

    /**
     * @var ClassifierInterface
     */
    protected $classifier;


    /**
     * @return (null|string)[][]
     *
     * @psalm-return array{0: array{0: string, 1: string}, 1: array{0: string, 1: string}, 2: array{0: string, 1: string}, 3: array{0: string, 1: null}}
     */
    public function matchProvider()
    {
        return [
            // perfect match
            ['dormir a l hotel', 'dormir_dehors'],
            ['je veux me coucher chez paul', 'dormir_amis'],
            ['je vais chez le concessionnaire', 'acheter_voiture'],
            ['', null]
        ];
    }

    /**
     * @param $question
     * @param $match
     *
     * @dataProvider matchProvider
     */
    public function testMatch($question, $match)
    {
        $result = $this->classifier->classify($question)->getResultsWithMinimumScore();
        if ($match === null) {
            $this->assertEmpty($result);
            return;
        }

        $this->assertNotEmpty($result);
        $this->assertNotNull($result[0]);
        $this->assertEquals(
            $result[0]->getIntent(),
            $match,
            sprintf(
                'Should match "%s" but match "%s" with score %f',
                $match,
                $result[0]->getIntent(),
                $result[0]->getScore()
            )
        );
    }
}
