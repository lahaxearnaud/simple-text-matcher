<?php


namespace Alahaxe\SimpleTextMatcher\Tests\Loader;


use Alahaxe\SimpleTextMatcher\Handlers\AbstractHandler;
use Alahaxe\SimpleTextMatcher\Message;

/**
 * Class DummyHandler
 *
 * @package alahaxe\SimpleTextMatcher\Tests\Loader
 */
class DummyHandler extends AbstractHandler
{

    /**
     * @return string
     */
    protected static function intentName(): string
    {
        return 'dummy';
    }

    /**
     * @param Message $message
     *
     * @return mixed
     */
    public function handle(Message $message)
    {
        $message->setResponses(['test']);
    }
}
