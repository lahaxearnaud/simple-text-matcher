<?php

namespace Alahaxe\SimpleTextMatcher\Handlers;

use Alahaxe\SimpleTextMatcher\Message;

/**
 * A very simple way to create an handler
 *
 * @package Alahaxe\SimpleTextMatcher\Handlers
 */
class ClosureHandler extends AbstractHandler
{
    /**
     * @var string
     */
    protected $intentName;

    /**
     * @var \Closure
     */
    protected $closure;

    /**
     * ClosureHandler constructor.
     *
     * @param string $intentName
     * @param \Closure $closure
     */
    public function __construct(string $intentName, \Closure $closure)
    {
        $this->intentName = $intentName;
        $this->closure = $closure;
    }

    /**
     * @return string
     */
    protected static function intentName():string
    {
        return AbstractHandler::DEFAULT_INTENT_NAME; // all intents are listen and we sort only in handle
    }

    /**
     * @param Message $message
     *
     * @return mixed|void
     */
    public function handle(Message $message)
    {
        if ($this->intentName !== AbstractHandler::DEFAULT_INTENT_NAME && $message->getIntentDetected() !== $this->intentName) {
            return;
        }

        call_user_func($this->closure, $message);
    }
}
