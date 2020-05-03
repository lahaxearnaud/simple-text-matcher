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
class NoMatchHandlerTest extends AbstractHandlerTest
{
    public function getQuestion(): string
    {
        return 'dsdkjdjksdj';
    }

    /**
     * @inheritDoc
     */
    public function registerHandler(): void
    {
        $this->engine->registerHandler(new ClosureHandler('manger', static function (Message $message) {
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
        $this->assertFalse($message->hasResponses());
        $responses = $message->getResponses();
        $this->assertIsArray($responses);
        $this->assertEmpty($responses);
    }
}
