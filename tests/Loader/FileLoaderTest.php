<?php


namespace Alahaxe\SimpleTextMatcher\Tests\Loader;

use Alahaxe\SimpleTextMatcher\EngineFactory;
use Alahaxe\SimpleTextMatcher\Loader\FileLoader;
use Alahaxe\SimpleTextMatcher\Loader\LoaderException;
use Alahaxe\SimpleTextMatcher\Loader\UserModel;
use PHPUnit\Framework\TestCase;

class FileLoaderTest extends TestCase
{

    public function testValidFolder()
    {
        $loader = new FileLoader(__DIR__.'/../../example');
        $model = $loader->load();
        $this->assertNotNull($model);
        $this->assertInstanceOf(UserModel::class, $model);
        $this->assertNotEmpty($model->getIntentExtractors());
        $this->assertNotEmpty($model->getSynonyms());
        $this->assertNotEmpty($model->getTraining());
        $this->assertNotEmpty($model->getIntentHandlers());

        $engine = (new EngineFactory(__CLASS__))->build();
        $engine->prepareWithLoader($loader);
    }

    public function testInValidFolder()
    {
        $this->expectException(LoaderException::class);
        $loader = new FileLoader(__DIR__.'/../../foobar');
        $loader->load();
    }
}
