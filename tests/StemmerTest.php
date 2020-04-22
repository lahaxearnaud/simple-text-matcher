<?php


namespace Alahaxe\SimpleTextMatcher\Tests;

use Alahaxe\SimpleTextMatcher\Classifiers\ClassifiersBag;
use Alahaxe\SimpleTextMatcher\Classifiers\JaroWinklerClassifier;
use Alahaxe\SimpleTextMatcher\Classifiers\LevenshteinClassifier;
use Alahaxe\SimpleTextMatcher\Classifiers\NaiveBayesClassifier;
use Alahaxe\SimpleTextMatcher\Classifiers\SmithWatermanGotohClassifier;
use Alahaxe\SimpleTextMatcher\Classifiers\TrainedRegexClassifier;
use Alahaxe\SimpleTextMatcher\Engine;
use Alahaxe\SimpleTextMatcher\Entities\EmailExtractor;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorsBag;
use Alahaxe\SimpleTextMatcher\Entities\NumberExtractor;
use Alahaxe\SimpleTextMatcher\Entities\PhoneNumberExtractor;
use Alahaxe\SimpleTextMatcher\ModelBuilder;
use Alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use Alahaxe\SimpleTextMatcher\Normalizers\QuotesNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\StopwordsNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\UnpunctuateNormalizer;
use Alahaxe\SimpleTextMatcher\Stemmer;
use Alahaxe\SimpleTextMatcher\Subscribers\StemmerCacheSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class StemmerTest extends TestCase
{
    const TRAINING_DATA_CACHE = '/tmp/cache.json';

    public function testStemWord(): void
    {
        $stemmer = new Stemmer();
        $this->assertEquals('voitur', $stemmer->stem('voitures'));
        $this->assertEquals('cour', $stemmer->stem('couraient'));
        $this->assertEquals('chocolat', $stemmer->stem('chocolat'));
    }

    public function testStemSentence(): void
    {
        $stemmer = new Stemmer();
        $this->assertEquals('il chantent dan un champ de fleur', $stemmer->stemPhrase('ils chantent dans un champs de fleurs'));
        $this->assertEquals('je vais cherch du pain à la boulanger', $stemmer->stemPhrase('je vais chercher du pain à la boulangerie'));
    }

    public function testCache(): void
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

        $extractorBag = new EntityExtractorsBag();

        $extractorBag
            ->add(new NumberExtractor())
        ;

        $eventDispatcher = new EventDispatcher();

        $eventDispatcher->addSubscriber(new StemmerCacheSubscriber($cachePath));
        $engine = new Engine(
            $eventDispatcher,
            new ModelBuilder($normalizerBag),
            $normalizerBag,
            $classifierBag,
            $extractorBag,
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
            $extractorBag,
            new Stemmer(),
            self::TRAINING_DATA_CACHE
        );

        $cache = $engine->getStemmer()->getCache();
        $this->assertIsArray($cache);
        // 6: 4 words + 2 sentences
        $this->assertCount(6, $cache);
    }
}
