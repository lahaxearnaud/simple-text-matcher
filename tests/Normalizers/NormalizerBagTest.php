<?php

namespace Alahaxe\SimpleTextMatcher\Tests\Normalizers;

use Alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use Alahaxe\SimpleTextMatcher\Normalizers\QuotesNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\StopwordsNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\UnpunctuateNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * Class NormalizerBagTest
 *
 * @package Alahaxe\SimpleTextMatcher\Tests\Normalizers
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
            ->add(new TypoNormalizer());
    }

    public function testCount(): void
    {
        $this->assertEquals(6, $this->bag->count());
        $this->assertEquals(6, count($this->bag->all()));
    }

    public function testArrayAccess(): void
    {
        $this->assertInstanceOf(LowerCaseNormalizer::class, $this->bag[0]);
        $this->bag[99] = new LowerCaseNormalizer();
        $this->assertInstanceOf(LowerCaseNormalizer::class, $this->bag[99]);
        unset($this->bag[99]);
        $this->assertFalse(isset($this->bag[99]));
    }

    public function testPriority(): void
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
