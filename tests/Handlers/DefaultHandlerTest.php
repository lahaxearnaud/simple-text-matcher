<?php


namespace alahaxe\SimpleTextMatcher\Tests\Handlers;

use Alahaxe\SimpleTextMatcher\Handlers\AbstractHandler;
use Alahaxe\SimpleTextMatcher\Handlers\ClosureHandler;
use Alahaxe\SimpleTextMatcher\Message;

require_once __DIR__.'/AbstractHandlerTest.php';

/**
 * Class DefaultHandlerTest
 *
 * @package alahaxe\SimpleTextMatcher\Tests\Handlers
 */
class DefaultHandlerTest extends AbstractHandlerTest
{

    /**
     * @inheritDoc
     */
    public function registerHandler(): void
    {
        $this->engine->registerHandler(new ClosureHandler(AbstractHandler::DEFAULT_INTENT_NAME, static function (Message $message) {
            $message->setResponses([
                'DEFAULT'
            ]);
        }));
    }

    /**
     * @inheritDoc
     */
    public function checkResponse(Message $message): void
    {
        $this->assertTrue($message->hasResponses());
        $responses = $message->getResponses();
        $this->assertIsArray($responses);
        $this->assertNotEmpty($responses);
        $this->assertArrayHasKey(0, $responses);
        $this->assertEquals('DEFAULT', $responses[0]);
    }
}
