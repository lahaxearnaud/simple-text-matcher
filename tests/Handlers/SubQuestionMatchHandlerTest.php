<?php


namespace alahaxe\SimpleTextMatcher\Tests\Handlers;

use Alahaxe\SimpleTextMatcher\Handlers\ClosureHandler;
use Alahaxe\SimpleTextMatcher\Message;

require_once __DIR__.'/AbstractHandlerTest.php';

/**
 * Class SubQuestionMatchHandlerTest
 *
 * @package alahaxe\SimpleTextMatcher\Tests\Handlers
 */
class SubQuestionMatchHandlerTest extends AbstractHandlerTest
{
    public function getQuestion(): string
    {
        return 'acheter une voiture et passer la nuit au camping';
    }

    /**
     * @inheritDoc
     */
    public function registerHandler(): void
    {
        $this->engine->registerHandler(new ClosureHandler('dormir_dehors', static function (Message $message) {
            $message->setResponses([
                'DORMIR'
            ]);
        }));

        $this->engine->registerHandler(new ClosureHandler('acheter_voiture', static function (Message $message) {
            $message->setResponses([
                'MANGER'
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

        foreach ($message->getSubMessages() as $subMessage) {
            $this->assertIsArray($subMessage->getResponses());
            $this->assertNotEmpty($subMessage->getResponses());
        }
    }
}
