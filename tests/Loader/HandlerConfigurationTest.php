<?php

namespace Alahaxe\SimpleTextMatcher\Tests\Loader;

use Alahaxe\SimpleTextMatcher\Handlers\ClosureHandler;
use Alahaxe\SimpleTextMatcher\Loader\MemoryLoader;
use Alahaxe\SimpleTextMatcher\Loader\UserModel;
use Alahaxe\SimpleTextMatcher\Message;
use PHPUnit\Framework\TestCase;

/**
 * Class HandlerConfigurationTest
 * @package Alahaxe\SimpleTextMatcher\Tests\Loader
 */
class HandlerConfigurationTest extends TestCase
{

    public function testDirectAnswerHandler()
    {
        $memoryLoader = new MemoryLoader(new UserModel([], [], [], []));
        $memoryLoader->load();

        $result = $memoryLoader->convertConfigHandlerToHandlerInstance('test', [
            'directAnswer' => 'test'
        ]);

        $this->assertInstanceOf(ClosureHandler::class, $result);
        $message = new Message('Hello');
        $message->setIntentDetected('test');
        $result->handle($message);
        $this->assertIsArray($message->getResponses());
        $this->assertNotEmpty($message->getResponses());
        $this->assertEquals('test', $message->getResponses()[0]);

    }

    public function testClosureHandler()
    {
        $memoryLoader = new MemoryLoader(new UserModel([], [], [], []));
        $memoryLoader->load();

        $result = $memoryLoader->convertConfigHandlerToHandlerInstance('test', [
            'handlerClosure' => static function (Message $message) {
                $message->setResponses([
                    'test'
                ]);
            }
        ]);

        $this->assertInstanceOf(ClosureHandler::class, $result);

        $message = new Message('Hello');
        $message->setIntentDetected('test');
        $result->handle($message);
        $this->assertIsArray($message->getResponses());
        $this->assertNotEmpty($message->getResponses());
        $this->assertEquals('test', $message->getResponses()[0]);
    }

    public function testHandlerInstance()
    {
        $memoryLoader = new MemoryLoader(new UserModel([], [], [], []));
        $memoryLoader->load();

        $handler = new ClosureHandler('test', static function (Message $message) {
            $message->setResponses([
                'test'
            ]);
        });

        $result = $memoryLoader->convertConfigHandlerToHandlerInstance('test', [
            'handler' => $handler
        ]);

        $this->assertInstanceOf(ClosureHandler::class, $result);

        $message = new Message('Hello');
        $message->setIntentDetected('test');
        $result->handle($message);
        $this->assertIsArray($message->getResponses());
        $this->assertNotEmpty($message->getResponses());
        $this->assertEquals('test', $message->getResponses()[0]);
    }

    public function testHandlerClass()
    {
        $memoryLoader = new MemoryLoader(new UserModel([], [], [], []));

        $result = $memoryLoader->convertConfigHandlerToHandlerInstance('test', [
            'classHandler' => DummyHandler::class
        ]);

        $this->assertInstanceOf(DummyHandler::class, $result);
        $message = new Message('Hello');
        $message->setIntentDetected('test');
        $result->handle($message);
        $this->assertIsArray($message->getResponses());
        $this->assertNotEmpty($message->getResponses());
        $this->assertEquals('test', $message->getResponses()[0]);
    }

    public function testBadHandlerConfig()
    {
        $this->expectException(\LogicException::class);
        $memoryLoader = new MemoryLoader(new UserModel([], [], [], []));
        $memoryLoader->convertConfigHandlerToHandlerInstance('test', [
            'foo' => DummyHandler::class
        ]);
    }
}
