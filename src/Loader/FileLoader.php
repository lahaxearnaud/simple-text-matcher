<?php


namespace Alahaxe\SimpleTextMatcher\Loader;


use Alahaxe\SimpleTextMatcher\Handlers\AbstractHandler;
use Alahaxe\SimpleTextMatcher\Handlers\ClosureHandler;
use Alahaxe\SimpleTextMatcher\Message;
use Symfony\Component\Finder\Finder;

/**
 * Class FileLoader
 *
 * @package Alahaxe\SimpleTextMatcher\Loader
 */
class FileLoader
{
    /**
     * @var string
     */
    protected $rootModelDirectory;

    /**
     * FileLoader constructor.
     * @param string $rootModelDirectory
     */
    public function __construct(string $rootModelDirectory)
    {
        $this->rootModelDirectory = $rootModelDirectory;
    }

    /**
     * @return array
     */
    public function load():array
    {
        $result = [
            'training' => [],
            'synonyms' => [],
            'intentExtractors' => [],
            'intentHandlers' => []
        ];

        $result['synonyms'] = require($this->rootModelDirectory.'/synonyms.php');

        $finder = new Finder();
        $finder->in($this->rootModelDirectory.'/intents')
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->depth(' <= 4')
            ->name('*.php')
            ->files();

        foreach ($finder as $intentFile) {
            $config = require $intentFile->getRealPath();
            if (!isset($config['name'])) {
                continue;
            }

            $config = array_merge([
                'extractors' => [],
                'handler' => [],
                'training' => [],
            ], $config);

            $result['training'][$config['name']] = $config['training'];
            $result['intentExtractors'][$config['name']] = $config['extractors'];
            $result['intentHandlers'][$config['name']] = $this->convertConfigHandlerToHandlerInstance($config['name'], $config['handler']);
        }

        file_put_contents(__DIR__.'/debug.php', var_export($result, true));
        return $result;
    }

    protected function convertConfigHandlerToHandlerInstance(string $intentName, array $handlerConfig)
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
