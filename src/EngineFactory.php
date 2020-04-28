<?php


namespace Alahaxe\SimpleTextMatcher;

use Alahaxe\SimpleTextMatcher\Classifiers\ClassifiersBag;
use Alahaxe\SimpleTextMatcher\Classifiers\NaiveBayesClassifier;
use Alahaxe\SimpleTextMatcher\Classifiers\PerfectMatchClassifier;
use Alahaxe\SimpleTextMatcher\Classifiers\TrainedRegexClassifier;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorsBag;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary\InsultExtractor;
use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\EmojiFlagDetector;
use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\FlagDetectorBag;
use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\InsultFlagDetector;
use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\NegationFlagDetector;
use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\QuestionFlagDetector;
use Alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use Alahaxe\SimpleTextMatcher\Normalizers\QuotesNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\SingularizeNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\StopwordsNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\UnpunctuateNormalizer;
use Alahaxe\SimpleTextMatcher\Subscribers\MessageSubscriber;
use Alahaxe\SimpleTextMatcher\Subscribers\ModelBuilderSynonymsLoaderSubscriber;
use Alahaxe\SimpleTextMatcher\Subscribers\ModelCacheSubscriber;
use Alahaxe\SimpleTextMatcher\Subscribers\StemmerCacheSubscriber;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class EngineFactory
 *
 * @package Alahaxe\SimpleTextMatcher
 */
class EngineFactory
{
    /**
     * @param string $lang
     * @param EventDispatcherInterface|null $eventDispatcher
     * @param bool $cache
     *
     * @return Engine
     */
    public function build(string $lang = 'fr', EventDispatcherInterface $eventDispatcher = null, $cache = true):Engine
    {
        $eventDispatcher = $eventDispatcher ?? new EventDispatcher();

        if ($cache) {
            $tmpCacheFolder = sys_get_temp_dir() . '/simple-text-matcher';
            if (!is_dir($tmpCacheFolder)) {
                mkdir($tmpCacheFolder);
            }

            $eventDispatcher->addSubscriber(new ModelCacheSubscriber($tmpCacheFolder . '/model_cache.json'));
            $eventDispatcher->addSubscriber(new StemmerCacheSubscriber($tmpCacheFolder . '/stemmer_cache.json'));
            $eventDispatcher->addSubscriber(new ModelBuilderSynonymsLoaderSubscriber($tmpCacheFolder . '/synonymes'));

            $eventDispatcher->addSubscriber(new MessageSubscriber(new FlagDetectorBag([
                new NegationFlagDetector(),
                new InsultFlagDetector($lang),
                new QuestionFlagDetector(),
                new EmojiFlagDetector(),
            ])));
        }

        $classifiers = new ClassifiersBag();
        $classifiers
            ->add(new NaiveBayesClassifier())
            ->add(new TrainedRegexClassifier())
            ->add(new PerfectMatchClassifier())
        ;

        $normalizers = new NormalizersBag();
        $normalizers->add(new LowerCaseNormalizer())
        //    ->add(new StopwordsNormalizer($lang))
            ->add(new UnaccentNormalizer())
            ->add(new UnpunctuateNormalizer())
            ->add(new QuotesNormalizer())
            ->add(new TypoNormalizer([], $lang))
            ->add(new SingularizeNormalizer($lang))
        ;

        $entityExtractors = new EntityExtractorsBag();

        return new Engine(
            $eventDispatcher,
            new ModelBuilder(null, $lang, true),
            $normalizers,
            $classifiers,
            $entityExtractors,
            new Stemmer()
        );
    }
}
