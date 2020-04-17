<?php
namespace alahaxe\SimpleTextMatcher\Tests\Classifiers;

use alahaxe\SimpleTextMatcher\Classifiers\TrainedRegexClassifier;
use alahaxe\SimpleTextMatcher\Stemmer;
use PHPUnit\Framework\TestCase;

/**
 * Class RegexClassifierTest
 * @package alahaxe\SimpleTextMatcher\Tests\Classifiers
 */
class RegexClassifierTest extends TestCase
{
    /**
     * @var TrainedRegexClassifier
     */
    protected $classifier;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $stemmer = new Stemmer();
        $this->classifier = new TrainedRegexClassifier($stemmer);
        // already expanded and normalized model
        $this->classifier->prepareModel([
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
        ]);
    }

    /**
     * @return array
     */
    public function matchProvider()
    {
        return [
            // perfect match
            ['dormir a l hotel', 'dormir_dehors'],
            ['je dormir chez jean', 'dormir_amis'],
            ['je vais chez le concessionnaire', 'acheter_voiture'],

            // with small alteration
            ['dormirais a l hotel', 'dormir_dehors'],
            ['dormirais a hotel', 'dormir_dehors'],
            ['dormirais hotel', 'dormir_dehors'],

            // with extra spaces
            ['dormirais           hotel', 'dormir_dehors'],
        ];
    }

    /**
     * @param $question
     * @param $match
     *
     * @dataProvider matchProvider
     *
     */
    public function testMatch($question, $match)
    {
        $result = $this->classifier->classify($question)->getResultsWithMinimumScore();
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
