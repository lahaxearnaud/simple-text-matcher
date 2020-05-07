<?php


namespace Alahaxe\SimpleTextMatcher\Loader;

use Alahaxe\SimpleTextMatcher\Handlers\AbstractHandler;
use Alahaxe\SimpleTextMatcher\Handlers\ClosureHandler;
use Alahaxe\SimpleTextMatcher\Message;

/**
 * Class AbstractLoader
 *
 * @package Alahaxe\SimpleTextMatcher\Loader
 */
abstract class AbstractLoader implements LoaderInterface
{
    /**
     * @param string $intentName
     * @param array $handlerConfig
     * @return ClosureHandler|mixed
     */
    public function convertConfigHandlerToHandlerInstance(string $intentName, array $handlerConfig)
    {
        if (isset($handlerConfig['handler']) && $handlerConfig['handler'] instanceof AbstractHandler) {
            return $handlerConfig['handler'];
        }

        if (isset($handlerConfig['directAnswer'])) {
            return new ClosureHandler($intentName, static function (Message $message) use ($handlerConfig) {
                $responses = $handlerConfig['directAnswer'];
                if (!is_array($responses)) {
                    $responses = [$responses];
                }

                $message->setResponses($responses);
            });
        }

        if (isset($handlerConfig['handlerClosure'])) {
            return new ClosureHandler($intentName, $handlerConfig['handlerClosure']);
        }

        // not so good because of DI
        if (isset($handlerConfig['classHandler'])) {
            return new $handlerConfig['classHandler'];
        }

        throw new \LogicException('Bad handler configuration for intent '.$intentName);
    }
}
