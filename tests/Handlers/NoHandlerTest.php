<?php


namespace alahaxe\SimpleTextMatcher\Tests\Handlers;

use Alahaxe\SimpleTextMatcher\Handlers\AbstractHandler;
use Alahaxe\SimpleTextMatcher\Handlers\ClosureHandler;
use Alahaxe\SimpleTextMatcher\Message;

require_once __DIR__.'/AbstractHandlerTest.php';

/**
 * Class NoHandlerTest
 *
 * @package alahaxe\SimpleTextMatcher\Tests\Handlers
 */
class NoHandlerTest extends AbstractHandlerTest
{

    /**
     * @inheritDoc
     */
    public function registerHandler(): void
    {
        $this->engine->registerHandler(new ClosureHandler('foobar', static function (Message $message) {
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
        $this->assertFalse($message->hasResponses());
        $responses = $message->getResponses();
        $this->assertIsArray($responses);
        $this->assertEmpty($responses);
    }
}
