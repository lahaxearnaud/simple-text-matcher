<?php

namespace alahaxe\SimpleTextMatcher\Tests\MessageFlags\Detectors;

use Alahaxe\SimpleTextMatcher\Message;
use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\AbstractFlagDetector;
use Alahaxe\SimpleTextMatcher\MessageFlags\Flag;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractFlagDetector
 * @package alahaxe\SimpleTextMatcher\Tests\MessageFlags\Detectors
 */
abstract class AbstractFlagDetectorTest extends TestCase
{

    /**
     * @var AbstractFlagDetector
     */
    protected $detector;

    protected function tearDown():void
    {
        parent::tearDown();

        unset($this->detector);
    }

    /**
     * @return array
     */
    public abstract function provideQuestions();

    /**
     * @param string $question
     * @param bool $result
     *
     * @dataProvider provideQuestions
     */
    public function testDetection(string $question, bool $result)
    {
        $this->assertEquals($result, $this->detector->detect(new Message($question)));
    }

    public function testBuildFlag()
    {
        $flag = $this->detector->buildFlag();
        $this->assertInstanceOf(Flag::class, $flag);
        $this->assertNotEmpty($flag->getName());
        $this->assertEquals($flag->getName(), $flag->jsonSerialize());
        $this->assertEquals($flag->getName(), $flag->__toString());
    }
}
