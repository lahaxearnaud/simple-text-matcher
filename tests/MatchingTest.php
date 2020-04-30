<?php

namespace alahaxe\SimpleTextMatcher\Tests;

use Alahaxe\SimpleTextMatcher\EngineFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class MatchingTest
 * @package alahaxe\SimpleTextMatcher\Tests
 */
class MatchingTest extends TestCase
{

    /**
     * @var \Alahaxe\SimpleTextMatcher\Engine
     */
    protected $engine;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        (new EngineFactory(md5(__CLASS__)))->clearCache();
    }

    protected function setUp():void
    {
        parent::setUp();

        $this->engine = (new EngineFactory(md5(__CLASS__)))->build('fr');
        $model = require(__DIR__.'/model.php');

        $this->engine->prepare($model['training'], $model['synonyms'], $model['intentExtractors']);
    }

    protected function tearDown():void
    {
        parent::tearDown();

        unset($this->engine);
    }


    public function matchingProvider()
    {
        return [
            [
                'je vais dormir chez les parents de paul cette nuit',
                'dormir_amis'
            ],
            [
                'cette nuit, je vais surement aller dormir chez lucette',
                'dormir_amis'
            ],
            [
                'avec thomas on va aller dormir chez ses parents',
                'dormir_amis'
            ],
            [
                'je voudrais allez dormir chez un copain',
                'dormir_amis'
            ],
            [
                'est-ce que je peux dormir chez raoul ?',
                'dormir_amis'
            ],
            [
                'Colette me demande si j\'ai le droit de dormir chez lui',
                'dormir_amis'
            ],
            [
                'les parents de juliette sont ok pour que j\'aille dormir chez elle ce soir',
                'dormir_amis'
            ],
            [
                'je viens de m acheter la dernière voiture de chez fiat',
                'acheter_voiture'
            ],
            [
                'voici ma nouvelle voiture',
                'acheter_voiture'
            ],
            [
                'le mois prochain je vais me payer une auto',
                'acheter_voiture'
            ],
            [
                'je viens d\'avoir le concessionaire au telephone ma nouvelle voiture est prête',
                'acheter_voiture'
            ],
            [
                'tu as vu ma nouvelle clio ?',
                'acheter_voiture'
            ],
            [
                'j\'ai vraiment la dalle ! on mange quoi ?',
                'manger'
            ],
            [
                'je souhaite manger un bon gros burger des familles',
                'manger'
            ],
            [
                'je bouffe un steack et des frites',
                'manger'
            ],
            [
                'à midi je vais manger une tartiflette avec des amis',
                'manger'
            ],
            [
                'Je vais passer la nuit dans un hôtel sur Nantes',
                'dormir_dehors'
            ],
            [
                'Je vais dormir dans un hôtel sur Nantes',
                'dormir_dehors'
            ],
            [
                'Je vais dormir dans un sous une tente au camping pendant 6 jours',
                'dormir_dehors'
            ],
            [
                'Je vais camper',
                'dormir_dehors'
            ],
            [
                'Je vais au lit',
                'dormir_maison'
            ],
            [
                'salut',
                'bonjour'
            ],
            [
                'bye',
                'aurevoir'
            ],
            [
                'comment vas-tu ?',
                'question_sante'
            ],
            [
                'quel est ton nom',
                'question_nom'
            ],
            [
                'raconte moi une blague',
                'blague'
            ],
            [
                'raconte moi une blagoune',
                'blague'
            ],
            [
                'c\'est qui ton créateur ?',
                'chef'
            ]
        ];
    }

    /**
     * @param $question
     * @param $intent
     *
     * @dataProvider matchingProvider
     *
     */
    public function testMatching(string $question, string  $intent)
    {
        $message = $this->engine->predict($question);

        $this->assertNotEmpty(
            $message->getIntentDetected(),
            sprintf('Question "%s" should match intent "%s" but not intent detected', $question, $intent)
        );
        $this->assertEquals(
            $message->getIntentDetected(),
            $intent,
            sprintf('Question "%s" should match intent "%s" but found "%s"', $question, $intent, $message->getIntentDetected())
        );
    }
}
