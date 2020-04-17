<?php

namespace alahaxe\SimpleTextMatcher\Tests\Normalizers;

use alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use alahaxe\SimpleTextMatcher\Normalizers\QuotesNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\StopwordsNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\UnpunctuateNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * Class NormalizerBagTest
 * @package alahaxe\SimpleTextMatcher\Tests\Normalizers
 */
class NormalizerBagTest extends TestCase
{

    /**
     * @var NormalizersBag
     */
    protected $bag;

    /**
     *
     */
    protected function setUp():void
    {
        parent::setUp();
        $this->bag = new NormalizersBag();

        $this->bag->add(new LowerCaseNormalizer())
            ->add(new StopwordsNormalizer())
            ->add(new UnaccentNormalizer())
            ->add(new UnpunctuateNormalizer())
            ->add(new QuotesNormalizer())
            ->add(new TypoNormalizer())
        ;
    }

    public function testCount()
    {
        $this->assertEquals(6, $this->bag->count());
        $this->assertEquals(6, count($this->bag->all()));
    }

    public function testArrayAccess()
    {
        $this->assertInstanceOf(LowerCaseNormalizer::class, $this->bag[0]);
        $this->bag[99] = new LowerCaseNormalizer();
        $this->assertInstanceOf(LowerCaseNormalizer::class, $this->bag[99]);
        unset($this->bag[99]);
        $this->assertFalse(isset($this->bag[99]));
    }

    public function testPriority()
    {
        $normalizers = $this->bag->getOrderedByPriority();
        $currentPriority = -1;
        foreach ($normalizers as $normalizer) {
            $priority = $normalizer->getPriority();
            $this->assertGreaterThanOrEqual(0, $priority);
            $this->assertLessThanOrEqual(255, $priority);
            $this->assertTrue($priority >= $currentPriority);
        }
    }
}
