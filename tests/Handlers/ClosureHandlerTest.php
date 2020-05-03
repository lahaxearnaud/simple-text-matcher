<?php

namespace alahaxe\SimpleTextMatcher\Tests\Handlers;

use Alahaxe\SimpleTextMatcher\Handlers\ClosureHandler;
use Alahaxe\SimpleTextMatcher\Message;

require_once __DIR__.'/AbstractHandlerTest.php';

/**
 * Class ClosureHandlerTest
 *
 * @package alahaxe\SimpleTextMatcher\Tests\Handlers
 */
class ClosureHandlerTest extends AbstractHandlerTest
{

    /**
     * @inheritDoc
     */
    public function registerHandler(): void
    {
        $this->engine->registerHandler(new ClosureHandler('acheter_voiture', static function (Message $message) {
            $message->setResponses([
                'OK'
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
        $this->assertEquals('OK', $responses[0]);
    }
}
