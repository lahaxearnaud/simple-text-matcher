<?php


namespace alahaxe\SimpleTextMatcher\Tests;


use alahaxe\SimpleTextMatcher\Stemmer;
use PHPUnit\Framework\TestCase;

class StemmerTest extends TestCase
{

    public function testStemWord()
    {
        $stemmer = new Stemmer();
        $this->assertEquals('voitur', $stemmer->stem('voitures'));
        $this->assertEquals('cour', $stemmer->stem('couraient'));
        $this->assertEquals('chocolat', $stemmer->stem('chocolat'));
    }

    public function testStemSentence()
    {
        $stemmer = new Stemmer();
        $this->assertEquals('il chantent dan un champ de fleur', $stemmer->stemPhrase('ils chantent dans un champs de fleurs'));
        $this->assertEquals('je vais cherch du pain à la boulanger', $stemmer->stemPhrase('je vais chercher du pain à la boulangerie'));
    }

    public function testCache()
    {
        $cachePath = '/tmp/'.uniqid(__CLASS__.__METHOD__);
        $stemmer = new Stemmer($cachePath);
        $stemmer->stem('voitures');
        $stemmer->stem('voitures'); // twice
        $stemmer->stem('couraient');
        $stemmer->stem('chocolat');

        unset($stemmer);
        $this->assertFileExists($cachePath);

        $stemmer = new Stemmer($cachePath);
        $cache = $stemmer->getCache();
        $this->assertIsArray($cache);
        $this->assertCount(3, $cache);
    }
}
