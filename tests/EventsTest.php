<?php

namespace alahaxe\SimpleTextMatcher\Tests;

use Alahaxe\SimpleTextMatcher\Engine;
use Alahaxe\SimpleTextMatcher\EngineFactory;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\CarBrandExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\NumberExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Whitelist\CurrencyExtractor;
use Alahaxe\SimpleTextMatcher\Events\BeforeModelBuildEvent;
use Alahaxe\SimpleTextMatcher\Events\EngineBuildedEvent;
use Alahaxe\SimpleTextMatcher\Events\EngineEvent;
use Alahaxe\SimpleTextMatcher\Events\EngineStartedEvent;
use Alahaxe\SimpleTextMatcher\Events\EntitiesExtractedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageClassifiedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageCorrectedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageReceivedEvent;
use Alahaxe\SimpleTextMatcher\Events\MessageSplittedEvent;
use Alahaxe\SimpleTextMatcher\Events\ModelEvent;
use Alahaxe\SimpleTextMatcher\Events\ModelExpandedEvent;
use Alahaxe\SimpleTextMatcher\Message;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;

require_once __DIR__.'/TestEventSubscriber.php';

/**
 * Class EventsTest
 * @package alahaxe\SimpleTextMatcher\Tests
 */
class EventsTest extends TestCase
{

    /**
     * @var \Alahaxe\SimpleTextMatcher\Engine
     */
    protected $engine;

    /**
     * @var TestEventSubscriber
     */
    protected $testEventSubscriber;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        (new EngineFactory(md5(__CLASS__)))->clearCache();
    }

    protected function setUp():void
    {
        parent::setUp();

        /** @var EventDispatcher $dispatcher */
        $dispatcher = new EventDispatcher();
        $this->testEventSubscriber = new TestEventSubscriber();
        $dispatcher->addSubscriber($this->testEventSubscriber);

        $this->engine = (new EngineFactory(md5(__CLASS__)))->build('fr', $dispatcher);
        $this->engine->getExtractors()
            ->add(new CarBrandExtractor())
        ;

        $this->engine->prepare([
            'acheter_voiture' => [
                'je viens de faire un crédit pour acheter une nouvelle voiture',
                "qu'est-ce que tu penses de la voiture que je viens d'acheter ?",
                "je vais vous prendre cette voiture"
            ],
            'manger' => [
                "j'ai cuisiné on peux manger",
                "à midi on va manger au restaurant",
                "au petit-déjeuner on va bruncher en couple",
                "au diner on va se faire au restaurant",
                "je vais manger chez les parents de jules",
                "je vais manger au restaurant avec les parents de alexi",
                "je mange un plat"
            ],
            'bonjour' => [
                'salut',
                'bonjour',
                'slt',
                'coucou',
                'Tcho',
                'hello',
                'bien le bonjour'
            ],
            'aurevoir' => [
                'bye',
                'à plus',
                'bonne journée',
                'à bientôt',
                'à demain',
                'à plus dans le bus',
                'à demain dans le train'
            ],
        ], [

        ], [
            'acheter_voiture' => [
                CarBrandExtractor::class
            ]
        ]);
    }

    protected function tearDown():void
    {
        parent::tearDown();

        unset($this->engine);
        unset($this->testEventDispatcher);
    }

    public function testEvents()
    {
        $question = 'je viens de m acheter la dernière voiture de chez fiat';
        $this->engine->predict($question);

        $events = $this->testEventSubscriber->getCollectedEvents();
        $eventsClasses = array_map(static function (Event $event) {
            return get_class($event);
        }, $events);

        $this->assertEquals(count($eventsClasses), count(array_unique($eventsClasses)));

        $this->assertNotEmpty($eventsClasses);
        $this->assertContains(BeforeModelBuildEvent::class, $eventsClasses);
        $this->assertContains(EngineBuildedEvent::class, $eventsClasses);
        $this->assertContains(EngineStartedEvent::class, $eventsClasses);
        $this->assertContains(EntitiesExtractedEvent::class, $eventsClasses);
        $this->assertContains(MessageClassifiedEvent::class, $eventsClasses);
        $this->assertContains(MessageCorrectedEvent::class, $eventsClasses);
        $this->assertContains(MessageReceivedEvent::class, $eventsClasses);
        $this->assertContains(ModelExpandedEvent::class, $eventsClasses);

        foreach ($events as $event) {
            if ($event instanceof MessageEvent) {
                $this->assertEquals($question, $event->getMessage()->getRawMessage());
            }

            if ($event instanceof EngineEvent) {
                $this->assertNotNull($event->getEngine());
                $this->assertInstanceOf(Engine::class, $event->getEngine());
            }

            if ($event instanceof ModelEvent) {
                $this->assertIsArray($event->getModel());
                $this->assertNotEmpty($event->getModel());
            }
        }
    }


    public function testEventSplit()
    {
        $question = 'je vais acheter une voiture de chez fiat et au diner on va se faire au restaurant';
        $this->engine->predict($question, true);

        $events = $this->testEventSubscriber->getCollectedEvents();
        $eventsClasses = array_map(static function (Event $event) {
            return get_class($event);
        }, $events);

        $this->assertNotEmpty($eventsClasses);
        $this->assertContains(MessageSplittedEvent::class, $eventsClasses);

        $nbSplitEvent = 0;
        foreach ($events as $event) {
            if ($event instanceof MessageSplittedEvent) {
                $this->assertInstanceOf(Message::class, $event->getMessage());
                $this->assertIsArray($event->getMessage()->getSubMessages());
                $this->assertCount(2, $event->getMessage()->getSubMessages());
                $nbSplitEvent++;
            }
        }

        $this->assertEquals(1, $nbSplitEvent);
    }
}
