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
    /**
     *
     */
    public function testPersistModel(): void
    {
        $factory = new EngineFactory();

        $engine = $factory->build();
        $this->assertNotEmpty($engine->getNormalizers()->all());
        $this->assertEmpty($engine->getExtractors()->all());
        $this->assertNotEmpty($engine->getClassifiers()->all());

        $engine->prepare(
            [
                'manger' => [
                    'donne moi à ~manger',
                    'je veux ~manger'
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
