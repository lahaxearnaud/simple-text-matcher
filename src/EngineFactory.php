<?php


namespace Alahaxe\SimpleTextMatcher;

use Alahaxe\SimpleTextMatcher\Classifiers\ClassifiersBag;
use Alahaxe\SimpleTextMatcher\Classifiers\InsultClassifier;
use Alahaxe\SimpleTextMatcher\Classifiers\NaiveBayesClassifier;
use Alahaxe\SimpleTextMatcher\Classifiers\NgramNaiveBayesClassifier;
use Alahaxe\SimpleTextMatcher\Classifiers\PerfectMatchClassifier;
use Alahaxe\SimpleTextMatcher\Classifiers\TrainedRegexClassifier;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorsBag;
use Alahaxe\SimpleTextMatcher\Handlers\AbstractHandler;
use Alahaxe\SimpleTextMatcher\Handlers\ClosureHandler;
use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\EmojiFlagDetector;
use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\FlagDetectorBag;
use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\InsultFlagDetector;
use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\NegationFlagDetector;
use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\QuestionFlagDetector;
use Alahaxe\SimpleTextMatcher\Normalizers\ExtraSpaceRemoverNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use Alahaxe\SimpleTextMatcher\Normalizers\QuotesNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\SingularizeNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\UnpunctuateNormalizer;
use Alahaxe\SimpleTextMatcher\Subscribers\ClassificationSubscriber;
use Alahaxe\SimpleTextMatcher\Subscribers\EntitySubscriber;
use Alahaxe\SimpleTextMatcher\Subscribers\HandlerSubscriber;
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
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EngineFactory
{

    /**
     * @var string
     */
    protected $cacheUniqKey = '';

    /**
     * EngineFactory constructor.
     * @param string $cacheUniqKey
     */
    public function __construct(string $cacheUniqKey = '')
    {
        $this->cacheUniqKey = $cacheUniqKey;
    }

    /**
     * @return string
     */
    public function getCachePath() :string
    {
        $tmpCacheFolder = sys_get_temp_dir() . '/simple-text-matcher'.$this->cacheUniqKey;
        if (!is_dir($tmpCacheFolder)) {
            mkdir($tmpCacheFolder);
        }

        return $tmpCacheFolder;
    }

    /**
     * @param null $path
     */
    public function clearCache($path = null):void
    {
        $path = $path ?? $this->getCachePath();

        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? $this->clearCache($file) : unlink($file);
        }

        is_dir($path) && rmdir($path);

        return;
    }

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

        $eventDispatcher->addSubscriber(new ClassificationSubscriber());
        $eventDispatcher->addSubscriber(new EntitySubscriber());
        $eventDispatcher->addSubscriber(new HandlerSubscriber($eventDispatcher));
        $eventDispatcher->addSubscriber(new MessageSubscriber(new FlagDetectorBag([
            new NegationFlagDetector(),
            new InsultFlagDetector($lang),
            new QuestionFlagDetector(),
            new EmojiFlagDetector(),
        ])));

        $eventDispatcher->addSubscriber(new ClosureHandler(AbstractHandler::INSULT_INTENT_NAME, static function (Message $message) {
            $message->setResponses([
                '...'
            ]);
        }));

        if ($cache) {
            $tmpCacheFolder = $this->getCachePath();
            $eventDispatcher->addSubscriber(new ModelCacheSubscriber($tmpCacheFolder . '/model_cache.json'));
            $eventDispatcher->addSubscriber(new StemmerCacheSubscriber($tmpCacheFolder . '/stemmer_cache.json'));
            $eventDispatcher->addSubscriber(new ModelBuilderSynonymsLoaderSubscriber($tmpCacheFolder . '/synonymes'));
        }

        $classifiers = new ClassifiersBag();
        $classifiers
            ->add(new InsultClassifier()) // based on flag (set by InsultFlagDetector)
            ->add(new PerfectMatchClassifier()) // faster one
            ->add(new NaiveBayesClassifier()) // fast and quite relevant
            ->add(new NgramNaiveBayesClassifier()) // generate and works on bigrams
            ->add(new TrainedRegexClassifier()) // fast but a little bit less relevant than NaiveBayesClassifier
        ;

        $normalizers = new NormalizersBag();
        $normalizers->add(new LowerCaseNormalizer())
        //    ->add(new StopwordsNormalizer($lang))
            ->add(new ExtraSpaceRemoverNormalizer())
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
