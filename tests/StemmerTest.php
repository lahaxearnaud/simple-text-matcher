<?php


namespace alahaxe\SimpleTextMatcher\Tests;

use alahaxe\SimpleTextMatcher\Classifiers\ClassifiersBag;
use alahaxe\SimpleTextMatcher\Classifiers\JaroWinklerClassifier;
use alahaxe\SimpleTextMatcher\Classifiers\LevenshteinClassifier;
use alahaxe\SimpleTextMatcher\Classifiers\NaiveBayesClassifier;
use alahaxe\SimpleTextMatcher\Classifiers\SmithWatermanGotohClassifier;
use alahaxe\SimpleTextMatcher\Classifiers\TrainedRegexClassifier;
use alahaxe\SimpleTextMatcher\Engine;
use alahaxe\SimpleTextMatcher\ModelBuilder;
use alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use alahaxe\SimpleTextMatcher\Normalizers\QuotesNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\StopwordsNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\UnpunctuateNormalizer;
use alahaxe\SimpleTextMatcher\Stemmer;
use alahaxe\SimpleTextMatcher\Subscribers\StemmerCacheSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class StemmerTest extends TestCase
{
    const TRAINING_DATA_CACHE = '/tmp/cache.json';

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
        $cachePath = '/tmp/'.__CLASS__.__METHOD__;
        if (file_exists($cachePath)) {
            unlink($cachePath);
        }

        if (file_exists(self::TRAINING_DATA_CACHE)) {
            unlink(self::TRAINING_DATA_CACHE);
        }

        $normalizerBag = new NormalizersBag();
        $classifierBag = new ClassifiersBag();

        $classifierBag
            ->add(new TrainedRegexClassifier());

        $normalizerBag
            ->add(new LowerCaseNormalizer());

        $eventDispatcher = new EventDispatcher();

        $eventDispatcher->addSubscriber(new StemmerCacheSubscriber($cachePath));
        $engine = new Engine(
            $eventDispatcher,
            new ModelBuilder($normalizerBag),
            $normalizerBag,
            $classifierBag,
            new Stemmer(),
            self::TRAINING_DATA_CACHE
        );
        $engine->prepare(
            [
            'aaa' => [
                'voitures chocolat',
                'couraient voiture',
            ],
            ],
            []
        );

        $this->assertFileExists($cachePath);

        unset($engine);

        $engine = new Engine(
            $eventDispatcher,
            new ModelBuilder($normalizerBag),
            $normalizerBag,
            $classifierBag,
            new Stemmer(),
            self::TRAINING_DATA_CACHE
        );

        $cache = $engine->getStemmer()->getCache();
        $this->assertIsArray($cache);
        // 6: 4 words + 2 sentences
        $this->assertCount(6, $cache);
    }
}
