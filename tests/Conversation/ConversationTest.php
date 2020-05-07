<?php

namespace alahaxe\SimpleTextMatcher\Tests\Conversation;

use Alahaxe\SimpleTextMatcher\Conversation\Conversation;
use Alahaxe\SimpleTextMatcher\EngineFactory;
use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use Alahaxe\SimpleTextMatcher\Loader\FileLoader;
use Alahaxe\SimpleTextMatcher\Message;
use PHPUnit\Framework\TestCase;

class ConversationTest extends TestCase
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

        $loader = new FileLoader(__DIR__ . '/../../example');
        $this->engine->prepareWithLoader($loader);
    }

    public function testMoreOrLessGame()
    {
        $message = new Message('Je veux jouer');
        $this->engine->predict($message);
        $this->assertEquals('game', $message->getIntentDetected());
        $this->assertStringContainsString('un nombre', $message->getResponses()[0]);
        $this->assertTrue($message->isExpectAnswer());

        $this->assertInstanceOf(Conversation::class, $message->getConversation());
        $this->assertNotNull($message->getConversation());
        $conversationToken = $message->getConversationToken();
        $this->assertNotNull($conversationToken);

        $answer = (int) date('H');

        $message = new Message($answer);
        $message->setConversationToken($conversationToken);
        $this->engine->predict($message);

        $this->assertStringContainsString($answer, $message->getResponses()[0]);
        $this->assertStringContainsString('Gagné', $message->getResponses()[1]);
        $this->assertFalse($message->isExpectAnswer());

    }

    public function testCancelConversation()
    {
        $message = new Message('Je veux jouer');
        $this->engine->predict($message);
        $this->assertEquals('game', $message->getIntentDetected());
        $this->assertStringContainsString('un nombre', $message->getResponses()[0]);
        $this->assertTrue($message->isExpectAnswer());

        $this->assertInstanceOf(Conversation::class, $message->getConversation());
        $this->assertNotNull($message->getConversation());
        $conversationToken = $message->getConversationToken();
        $this->assertNotNull($conversationToken);

        $message = new Message('stop');
        $message->setConversationToken($conversationToken);
        $this->engine->predict($message);
        $this->assertStringContainsString('on oublie', $message->getResponses()[0]);
        $this->assertFalse($message->isExpectAnswer());
    }

    public function testSetNullTokenOnMessage()
    {
        $message = new Message('foo');
        $message->setConversationToken(null);
        $this->assertNull($message->getConversation());
    }

    public function testRebuildFromToken()
    {
        $entities = new EntityBag([
            new Entity('int', 12, 'foo')
        ]);
        $conversation = new Conversation('toto', 'id', $entities, 'bar');

        $token = $conversation->getToken();
        unset($conversation);

        $conversation = Conversation::buildFromToken($token);
        $this->assertEquals('toto', $conversation->getIntent());
        $this->assertEquals('id', $conversation->getConversationId());
        $this->assertEquals(1, $conversation->getEntities()->count());
        $this->assertEquals(12, $conversation->getEntities()->first()->getValue());
    }

    public function testRebuildFromInvalidToken()
    {
        $this->expectException(\InvalidArgumentException::class);
        Conversation::buildFromToken('foo');
    }
}
