<?php

namespace alahaxe\SimpleTextMatcher\Tests;

use alahaxe\SimpleTextMatcher\Classifiers\ClassifiersBag;
use alahaxe\SimpleTextMatcher\Classifiers\JaroWinklerClassifier;
use alahaxe\SimpleTextMatcher\Classifiers\LevenshteinClassifier;
use alahaxe\SimpleTextMatcher\Classifiers\NaiveBayesClassifier;
use alahaxe\SimpleTextMatcher\Classifiers\SmithWatermanGotohClassifier;
use alahaxe\SimpleTextMatcher\Classifiers\TrainedRegexClassifier;
use alahaxe\SimpleTextMatcher\Engine;
use alahaxe\SimpleTextMatcher\Message;
use alahaxe\SimpleTextMatcher\ModelBuilder;
use alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use alahaxe\SimpleTextMatcher\Normalizers\QuotesNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\StopwordsNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\UnpunctuateNormalizer;
use alahaxe\SimpleTextMatcher\Stemmer;
use alahaxe\SimpleTextMatcher\Subscribers\ModelBuilderSynonymsLoaderSubscriber;
use alahaxe\SimpleTextMatcher\Subscribers\ModelCacheSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

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

    /**
     * @return Engine
     */
    protected function buildEngine()
    {
        $normalizerBag = new NormalizersBag();
        $classifierBag = new ClassifiersBag();
        $stemmer = new Stemmer();

        $classifierBag
            ->add(new TrainedRegexClassifier($stemmer))
            ->add(new NaiveBayesClassifier($stemmer))
            ->add(new JaroWinklerClassifier($stemmer))
            ->add(new LevenshteinClassifier($stemmer))
            ->add(new SmithWatermanGotohClassifier($stemmer));

        $this->assertEquals(5, $classifierBag->count());

        $normalizerBag
            ->add(new LowerCaseNormalizer())
            ->add(new StopwordsNormalizer())
            ->add(new UnaccentNormalizer())
            ->add(new UnpunctuateNormalizer())
            ->add(new QuotesNormalizer())
            ->add(new TypoNormalizer());

        $this->assertEquals(6, $normalizerBag->count());

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new ModelCacheSubscriber(self::TRAINING_DATA_CACHE));
        $eventDispatcher->addSubscriber(new ModelBuilderSynonymsLoaderSubscriber());

        return new Engine(
            $eventDispatcher,
            new ModelBuilder($normalizerBag),
            $normalizerBag,
            $classifierBag,
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
        $this->assertNotEmpty($result->getRawMessage());
        $this->assertEquals($result->getRawMessage(), $question);
        $this->assertNotEmpty($result->getNormalizedMessage());
        $this->assertNotEmpty($result->getMessageId());
        $this->assertGreaterThan(0, $result->getClassification()->count());
        $this->assertEquals($match, $result->getIntentDetected());
        $this->assertGreaterThan(0, $result->getClassification()[0]->getDuration());

        $jsonResult = json_decode(json_encode($result->getClassification()), true);
        $this->assertIsArray($jsonResult);

        $jsonResult = json_decode(json_encode($result), true);
        $this->assertIsArray($jsonResult);
        $this->assertArrayHasKey('performance', $jsonResult);
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

        foreach ($this->matchProvider() as list($question, $match)) {
            $result = $this->engine->predict($question);
            $this->assertGreaterThan(0, $result->getClassification()->count());
            $this->assertEquals($match, $result->getIntentDetected());
        }
    }
}
