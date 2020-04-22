<?php

namespace Alahaxe\SimpleTextMatcher\Tests\Normalizers;

use Alahaxe\SimpleTextMatcher\Normalizers\NormalizerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class TypoNormalizerTest
 *
 * @package Alahaxe\SimpleTextMatcher\Tests\Normalizers
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
    abstract public function correctProvider();

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
