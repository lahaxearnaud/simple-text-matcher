<?php

namespace alahaxe\SimpleTextMatcher\Tests\Conversation;

use Alahaxe\SimpleTextMatcher\Conversation\ConversationHelper;
use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Message;
use PHPUnit\Framework\TestCase;

class ConversationHelperTest extends TestCase
{

    public function testEntityAlreadyInBag()
    {
        $message = new Message('ok');
        $message->getEntities()->add(new Entity('int', 12, 'foo'));
        $conversationHelper = new ConversationHelper($message);
        $conversationHelper->askInt('foo', 'Foo ?');
        $this->assertEquals(1, $message->getEntities()->count());

        $conversationHelper->askNumber('foo', 'Foo ?');
        $this->assertEquals(1, $message->getEntities()->count());
    }

    public function testConfirm()
    {
        $message = new Message('ok');
        $conversationHelper = new ConversationHelper($message);

        $conversationHelper->confirm('confirm', 'Confirm ?');
        $this->assertGreaterThan(0, $message->getEntities()->count());
        $value = $message->getEntities()->getByName('confirm')->first()->getValue();
        $this->assertTrue($value);
    }

    public function testUrl()
    {
        $message = new Message('https://httpbin.org/get');
        $conversationHelper = new ConversationHelper($message);

        $conversationHelper->askUrl('url', 'URL ?');
        $this->assertGreaterThan(0, $message->getEntities()->count());
        $value = $message->getEntities()->getByName('url')->first()->getValue();
        $this->assertEquals('https://httpbin.org/get', $value);
    }

    public function testPercentage()
    {
        $message = new Message('6,87 %');
        $conversationHelper = new ConversationHelper($message);

        $conversationHelper->askPercentage('percentage', 'Pourcentage ?');
        $this->assertGreaterThan(0, $message->getEntities()->count());
        $value = $message->getEntities()->getByName('percentage')->first()->getValue();
        $this->assertEquals(6.87, $value);
    }

    public function testNotMatch()
    {
        $message = new Message('pouet');
        $conversationHelper = new ConversationHelper($message);
        $conversationHelper->askNumber('foo', 'Foo ?');
        $this->assertEquals(0, $message->getEntities()->count());
        $this->assertTrue($message->isExpectAnswer());
    }

    public function testResetedConversation()
    {
        $message = new Message('12');
        $conversationHelper = new ConversationHelper($message);
        $message->setConversation(null);
        $conversationHelper->askNumber('foo', 'Foo ?');
        $this->assertEquals(0, $message->getEntities()->count());
        $this->assertFalse($message->isExpectAnswer());
    }
}
