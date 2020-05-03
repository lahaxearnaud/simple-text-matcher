<?php

namespace Alahaxe\SimpleTextMatcher\Tests;

use Alahaxe\SimpleTextMatcher\Classifiers\ClassifiersBag;
use Alahaxe\SimpleTextMatcher\Classifiers\InsultClassifier;
use Alahaxe\SimpleTextMatcher\Classifiers\JaroWinklerClassifier;
use Alahaxe\SimpleTextMatcher\Classifiers\LevenshteinClassifier;
use Alahaxe\SimpleTextMatcher\Classifiers\NaiveBayesClassifier;
use Alahaxe\SimpleTextMatcher\Classifiers\SmithWatermanGotohClassifier;
use Alahaxe\SimpleTextMatcher\Classifiers\TrainedRegexClassifier;
use Alahaxe\SimpleTextMatcher\Engine;
use Alahaxe\SimpleTextMatcher\EngineFactory;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorsBag;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\EmailExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\NumberExtractor;
use Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex\PhoneNumberExtractor;
use Alahaxe\SimpleTextMatcher\Message;
use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\FlagDetectorBag;
use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\NegationFlagDetector;
use Alahaxe\SimpleTextMatcher\MessageFlags\FlagBag;
use Alahaxe\SimpleTextMatcher\ModelBuilder;
use Alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use Alahaxe\SimpleTextMatcher\Normalizers\QuotesNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\ReplaceNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\StopwordsNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\UnpunctuateNormalizer;
use Alahaxe\SimpleTextMatcher\Stemmer;
use Alahaxe\SimpleTextMatcher\Subscribers\ClassificationSubscriber;
use Alahaxe\SimpleTextMatcher\Subscribers\EntitySubscriber;
use Alahaxe\SimpleTextMatcher\Subscribers\LogSubscriber;
use Alahaxe\SimpleTextMatcher\Subscribers\MessageSubscriber;
use Alahaxe\SimpleTextMatcher\Subscribers\ModelBuilderSynonymsLoaderSubscriber;
use Alahaxe\SimpleTextMatcher\Subscribers\ModelCacheSubscriber;
use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class EngineTest
 * @package Alahaxe\SimpleTextMatcher\Tests
 */
class EngineTest extends TestCase
{
    const TRAINING_DATA_CACHE = '/tmp/cache.json';
    const TRAINING_DATA = [
        "dormir_dehors" => [
            "dormir a l hotel",
            "je vais dormir dans une auberge",
            "passer la nuit au camping"
        ],
        "dormir_amis" => [
            "avec jean on va dormir chez ses parent",
            "je veux me coucher chez paul",
            "je dormir chez jean",
        ],
        "acheter_voiture" => [
            "je vais chez le concessionnaire",
            "je ai repere une voiture je vais l'acheter",
        ]
    ];

    /**
     * @var Engine
     */
    protected $engine;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        (new EngineFactory())->clearCache();
    }


    /**
     * @return Engine
     */
    protected function buildEngine()
    {
        $normalizerBag = new NormalizersBag();
        $classifierBag = new ClassifiersBag();
        $extractorBag = new EntityExtractorsBag();
        $stemmer = new Stemmer();

        $extractorBag
            ->add(new NumberExtractor())
            ->add(new EmailExtractor())
            ->add(new PhoneNumberExtractor())
            ;

        $classifierBag
            ->add(new InsultClassifier())
            ->add(new TrainedRegexClassifier($stemmer))
            ->add(new NaiveBayesClassifier($stemmer))
            ->add(new JaroWinklerClassifier($stemmer))
            ->add(new LevenshteinClassifier($stemmer))
            ->add(new SmithWatermanGotohClassifier($stemmer));

        $this->assertEquals(6, $classifierBag->count());

        $normalizerBag
            ->add(new LowerCaseNormalizer())
            ->add(new StopwordsNormalizer())
            ->add(new UnaccentNormalizer())
            ->add(new UnpunctuateNormalizer())
            ->add(new QuotesNormalizer())
            ->add(new ReplaceNormalizer([
                'slt' => 'salut'
            ]))
            ->add(new TypoNormalizer());

        $this->assertEquals(7, $normalizerBag->count());

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new ClassificationSubscriber());
        $eventDispatcher->addSubscriber(new EntitySubscriber());
        $eventDispatcher->addSubscriber(new LogSubscriber(new TestLogger()));
        $eventDispatcher->addSubscriber(new ModelCacheSubscriber(self::TRAINING_DATA_CACHE));
        $eventDispatcher->addSubscriber(new ModelBuilderSynonymsLoaderSubscriber());
        $eventDispatcher->addSubscriber(new MessageSubscriber(new FlagDetectorBag([
            new NegationFlagDetector(),
        ])));

        return new Engine(
            $eventDispatcher,
            new ModelBuilder($normalizerBag),
            $normalizerBag,
            $classifierBag,
            $extractorBag,
            new Stemmer()
        );
    }

    /**
     *
     */
    protected function setUp():void
    {
        parent::setUp();

        if (file_exists(self::TRAINING_DATA_CACHE)) {
            unlink(self::TRAINING_DATA_CACHE);
        }

        $this->engine = $this->buildEngine();
        $this->engine->prepare(self::TRAINING_DATA, []);
    }

    protected function tearDown():void
    {
        parent::tearDown();

        if (file_exists(self::TRAINING_DATA_CACHE)) {
            unlink(self::TRAINING_DATA_CACHE);
        }

        unset($this->engine);
    }

    /**
     * @return string[][]
     *
     * @psalm-return array{0: array{0: string, 1: string}, 1: array{0: string, 1: string}, 2: array{0: string, 1: string}}
     */
    public function matchProvider(): array
    {
        return [
            // perfect match
            ['dormir a l hotel', 'dormir_dehors'],
            ['je veux me coucher chez paul', 'dormir_amis'],
            ['je vais chez le concessionnaire', 'acheter_voiture'],
        ];
    }

    /**
     * @param $question
     * @param $match
     *
     * @dataProvider matchProvider
     *
     * @return void
     */
    public function testMatch($question, $match): void
    {
        $result = $this->engine->predict($question);
        $this->assertInstanceOf(Message::class, $result);
        $this->assertFalse($result->hasSubMessages());
        $this->assertNotEmpty($result->getRawMessage());
        $this->assertEquals($result->getRawMessage(), $question);
        $this->assertNotEmpty($result->getNormalizedMessage());
        $this->assertNotEmpty($result->getMessageId());
        $this->assertGreaterThan(0, $result->getClassification()->count());
        $this->assertEquals($match, $result->getIntentDetected());
        $this->assertGreaterThan(0, $result->getClassification()[0]->getDuration());
        $this->assertGreaterThanOrEqual(0, $result->getNbWords());
        $this->assertEquals(count($result->getWords()), $result->getNbWords());
        $this->assertInstanceOf(FlagBag::class, $result->getFlags());
        $this->assertInstanceOf(EntityBag::class, $result->getEntities());

        $jsonResult = json_decode(json_encode($result->getClassification()), true);
        $this->assertIsArray($jsonResult);

        $jsonResult = json_decode(json_encode($result), true);
        $this->assertIsArray($jsonResult);
        $this->assertArrayHasKey('performance', $jsonResult);
        $this->assertArrayHasKey('flags', $jsonResult);
    }

    public function testPersistModel(): void
    {
        $this->assertFileExists(self::TRAINING_DATA_CACHE);
    }

    public function testReloadModel(): void
    {
        $this->assertFileExists(self::TRAINING_DATA_CACHE);

        $secondEngine = $this->buildEngine();
        $secondEngine->prepare(self::TRAINING_DATA, []);
        unset($secondEngine);

        foreach ($this->matchProvider() as list($question, $match)) {
            $result = $this->engine->predict($question);
            $this->assertGreaterThan(0, $result->getClassification()->count());
            $this->assertEquals($match, $result->getIntentDetected());
        }
    }

    /**
     * @coversNothing
     */
    public function testPerformanceReloadModel(): void
    {
        $this->assertFileExists(self::TRAINING_DATA_CACHE);

        $start = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            $secondEngine = $this->buildEngine();
            $secondEngine->prepare(self::TRAINING_DATA, []);
            unset($secondEngine);
        }

        // should be < 0.5 but the CI may be slower
        $this->assertLessThan(1, microtime(true) - $start);
    }

    /**
     * @return void
     */
    public function testMatchWithSubQuestion(): void
    {
        $result = $this->engine->predict('je vais chez le concessionnaire et après je vais dormir dans une auberge', true);
        $this->assertInstanceOf(Message::class, $result);
        $this->assertTrue($result->hasSubMessages());

        $subMessages = $result->getSubMessages();

        $this->assertInstanceOf(Message::class, $subMessages[0]);
        $this->assertInstanceOf(Message::class, $subMessages[1]);

        $this->assertEquals('acheter_voiture', $subMessages[0]->getIntentDetected());
        $this->assertEquals('dormir_dehors', $subMessages[1]->getIntentDetected());

        $this->assertEquals($result->getMessageId(), $subMessages[0]->getParentMessageId());
        $this->assertEquals($result->getMessageId(), $subMessages[1]->getParentMessageId());
    }

    /**
     *
     * @return void
     */
    public function testMatchWithOneMatchingSubQuestion(): void
    {
        $result = $this->engine->predict('je vais chez le concessionnaire et dskjdksj sdkjdskdj dskdskdj dskjdskj', true);
        $this->assertInstanceOf(Message::class, $result);
        $this->assertFalse($result->hasSubMessages());

        $this->assertEquals('acheter_voiture', $result->getIntentDetected());
    }

    /**
     *
     * @return void
     */
    public function testMatchWithNegativeQuestion(): void
    {
        $result = $this->engine->predict('je ne vais pas chez le concessionnaire', true);
        $this->assertInstanceOf(Message::class, $result);
        $this->assertFalse($result->hasSubMessages());
        $this->assertEquals('acheter_voiture', $result->getIntentDetected());
        $this->assertTrue($result->hasFlag(NegationFlagDetector::getFlagName()));
    }
}
