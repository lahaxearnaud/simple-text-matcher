<?php

namespace Alahaxe\SimpleTextMatcher\Tests;

use Alahaxe\SimpleTextMatcher\EngineFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class EngineFactoryTest
 * @package Alahaxe\SimpleTextMatcher\Tests
 */
class EngineFactoryTest extends TestCase
{
    protected function tearDown():void
    {
        parent::tearDown();

        (new EngineFactory(md5(__CLASS__)))->clearCache();
    }

    /**
     *
     */
    public function testCacheClear(): void
    {
        $factory = new EngineFactory(md5(__CLASS__));
        $cacheDir = $factory->getCachePath();
        $this->assertTrue(is_dir($cacheDir));
        $factory->clearCache();
        $this->assertFalse(is_dir($cacheDir));

        // test on clear on non existing folder
        $factory->clearCache();
        $this->assertFalse(is_dir($cacheDir));
    }

    /**
     *
     */
    public function testBuildEngine(): void
    {
        $factory = new EngineFactory(md5(__CLASS__));

        $engine = $factory->build();
        $this->assertInstanceOf(EventDispatcher::class, $engine->getEventDispatcher());
        $this->assertNotEmpty($engine->getNormalizers()->all());
        $this->assertEmpty($engine->getExtractors()->all());
        $this->assertNotEmpty($engine->getClassifiers()->all());

        $engine->prepare(
            [
                'manger' => [
                    'donne moi à ~manger',
                    'je veux ~manger',
                    'je souhaite manger une pomme'
                ],
                'chanter' => [
                    'je veux chanter',
                    'nous allons chanter dans les bois'
                ]
            ],
            [
                '~manger' => [
                    'manger',
                    'grailler',
                    'consommer de la nourriture'
                ]
            ]
        );

        $engine->predict('je veux manger');
    }
}
