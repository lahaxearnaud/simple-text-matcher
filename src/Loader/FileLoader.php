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
class FileLoader extends AbstractLoader
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
     * @return UserModel
     * @throws LoaderException
     */
    public function load():UserModel
    {
        $training = [];
        $intentExtractors = [];
        $intentHandlers = [];

        if (!is_dir($this->rootModelDirectory)) {
            throw new LoaderException();
        }

        $synonyms = require($this->rootModelDirectory.'/synonyms.php');

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

            $training[$config['name']] = $config['training'];
            $intentExtractors[$config['name']] = $config['extractors'];
            $intentHandlers[$config['name']] = $this->convertConfigHandlerToHandlerInstance($config['name'], $config['handler']);
        }

        return new UserModel($training, $synonyms, $intentExtractors, $intentHandlers);
    }

}
