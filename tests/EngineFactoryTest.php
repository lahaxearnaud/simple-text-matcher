<?php

namespace Alahaxe\SimpleTextMatcher\Tests;

use Alahaxe\SimpleTextMatcher\EngineFactory;
use PHPUnit\Framework\TestCase;

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
    public function testBuildEngine(): void
    {
        $factory = new EngineFactory(md5(__CLASS__));

        $engine = $factory->build();
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
