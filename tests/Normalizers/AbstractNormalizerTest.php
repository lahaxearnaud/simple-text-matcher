<?php

namespace alahaxe\SimpleTextMatcher\Tests\Normalizers;

use alahaxe\SimpleTextMatcher\Normalizers\NormalizerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class TypoNormalizerTest
 * @package alahaxe\SimpleTextMatcher\Tests\Normalizers
 */
abstract class AbstractNormalizerTest extends TestCase
{

    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * @return array
     */
    public abstract function correctProvider();

    /**
     * @param string $raw
     * @param string $corrected
     *
     * @dataProvider correctProvider
     */
    public function testNormalize(string $raw, string $corrected)
    {
        $this->assertEquals($corrected, $this->normalizer->normalize($raw));
    }
}
