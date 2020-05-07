<?php


namespace alahaxe\SimpleTextMatcher\Tests\Handlers;

use Alahaxe\SimpleTextMatcher\Engine;
use Alahaxe\SimpleTextMatcher\EngineFactory;
use Alahaxe\SimpleTextMatcher\Message;
use Alahaxe\SimpleTextMatcher\Subscribers\HandlerSubscriber;
use PHPUnit\Framework\TestCase;

abstract class AbstractHandlerTest extends TestCase
{
    const TRAINING_DATA = [
        "dormir_dehors" => [
            "dormir a l hotel",
            "je vais dormir dans une auberge",
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
     * @var Engine
     */
    protected $engine;

    protected function setUp():void
    {
        parent::setUp();

        $this->engine = (new EngineFactory(__CLASS__))->build();
        $this->engine->getEventDispatcher()->addSubscriber(new HandlerSubscriber($this->engine->getEventDispatcher()));
        $this->engine->prepare(self::TRAINING_DATA, [], []);
    }

    protected function tearDown():void
    {
        parent::tearDown();
        unset($this->engine);
    }

    /**
     * @return string
     */
    public function getQuestion():string
    {
        return 'je vais chez le concessionnaire';
    }

    abstract public function registerHandler():void;

    /**
     * @param Message $message
     */
    abstract public function checkResponse(Message $message):void;

    public function testHandler()
    {
        $this->registerHandler();

        $message = $this->engine->predict($this->getQuestion(), true);

        $this->checkResponse($message);
    }
}
