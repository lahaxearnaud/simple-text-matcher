<?php

namespace alahaxe\SimpleTextMatcher\Tests\Handlers;

use Alahaxe\SimpleTextMatcher\Message;

require_once __DIR__.'/AbstractHandlerTest.php';

/**
 * Class InsultHandlerTest
 *
 * @package alahaxe\SimpleTextMatcher\Tests\Handlers
 */
class InsultHandlerTest extends AbstractHandlerTest
{

    /**
     * @inheritDoc
     */
    public function registerHandler(): void
    {
    }

    public function getQuestion(): string
    {
        return 'grosse pute';
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
        $this->assertEquals('...', $responses[0]);
    }
}
