Simple Text Matcher
=============

Installation
----

    composer install alahaxe/simple-text-matcher

Glossary
----

- Training phrases: A set of sentenses associated to an intent
- Synonyms: Set of synonymes used to replace and multiply the size of training phrases
- Normalizer: Classes that remove usless informations from dataset and user question (punctuation, letter case, stopwords...)
- Stemmer: Remove the suffix from a word and reduce it to its root word
- Classifier: Algorithm that try to detect the intent from an user question

Usage
----

You can check a running example : https://github.com/lahaxearnaud/simple-text-matcher/blob/master/example.php

```php
<?php
    // build or retreive a SF event dispatcher
    $dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();

    // for a better performance you can add some cache lister
    $dispatcher->addSubscriber(new \alahaxe\SimpleTextMatcher\Subscribers\ModelCacheSubscriber(__DIR__.'/model_cache.json'));
    $dispatcher->addSubscriber(new \alahaxe\SimpleTextMatcher\Subscribers\StemmerCacheSubscriber(__DIR__.'/stemmer_cache.json'));
    $dispatcher->addSubscriber(new \alahaxe\SimpleTextMatcher\Subscribers\ModelBuilderSynonymsLoaderSubscriber(__DIR__.'/synonymes'));    
    $dispatcher->addSubscriber(new \alahaxe\SimpleTextMatcher\Subscribers\LogSubscriber($yourPSRLogger));

    // add some classifiers, pick some in the package or create a new one with your logic
    $classifiers = new \alahaxe\SimpleTextMatcher\Classifiers\ClassifiersBag();
    $classifiers
        ->add(new \alahaxe\SimpleTextMatcher\Classifiers\TrainedRegexClassifier()) // very fast
        ->add(new \alahaxe\SimpleTextMatcher\Classifiers\NaiveBayesClassifier())  // very fast
        ->add(new \alahaxe\SimpleTextMatcher\Classifiers\JaroWinklerClassifier())  // a little bit slow
        ->add(new \alahaxe\SimpleTextMatcher\Classifiers\LevenshteinClassifier())  // a little bit slow
        ->add(new \alahaxe\SimpleTextMatcher\Classifiers\SmithWatermanGotohClassifier()) // a little bit slow
        ;

    // add come normalizer to remove noise in the user question AND in training data
    $normalizers = new \alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag();
    $normalizers
        ->add(new \alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer())
        ->add(new \alahaxe\SimpleTextMatcher\Normalizers\StopwordsNormalizer())
        ->add(new \alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer())
        ->add(new \alahaxe\SimpleTextMatcher\Normalizers\UnpunctuateNormalizer())
        ->add(new \alahaxe\SimpleTextMatcher\Normalizers\QuotesNormalizer())
        ->add(new \alahaxe\SimpleTextMatcher\Normalizers\TypoNormalizer())
        ->add(new \alahaxe\SimpleTextMatcher\Normalizers\ReplaceNormalizer([
            'bagnole' => 'voiture',
            'slt' => 'salut',
        ]))
        ;

    // bild the engine
    $engine = new \alahaxe\SimpleTextMatcher\Engine(
        $dispatcher,
        new \alahaxe\SimpleTextMatcher\ModelBuilder($normalizers),
        $normalizers,
        $classifiers,
        new \alahaxe\SimpleTextMatcher\Stemmer()
    );

    // build the data model and all classifier models
    $engine->prepare($model = [
        "dormir_dehors" => [
            "dormir a l hotel",
            "~je vais dormir dans une auberge", // ~je wil be replaces by EVERY synonymes defined
            "passer la nuit au camping"
        ],
        "dormir_amis" => [
            "avec jean on va dormir chez ses parent",
            "~je veux me coucher chez paul",
            "~je dormir chez jean",
        ],
        "acheter_voiture" => [
            "~je vais chez le concessionnaire",
            "~je ai repere une ~voiture je vais l'acheter",
        ]
    ], $synonyms = [
        "~je" => [
            "je",
            "moi"
        ],
        "~voiture" => [
            "voiture",
            "auto"
        ]
    ]);

    $question = 'Je veux acheter une voiture';

    // wrap you request in a Message object, classification will be added in this object
    $message = new \alahaxe\SimpleTextMatcher\Message($question);

    // classify the message
    $engine->predict($message);


    echo 'Question: ' . $message->getRawMessage() . PHP_EOL;
    echo 'Normalized message: ' . $message->getNormalizedMessage() . PHP_EOL;
    echo 'Intent detected: ' . $message->getIntentDetected() . PHP_EOL;
```

Model
----

Cache and performance
----

The library provide the possibility to cache builded models and stemmer results.
[ModelCacheSubscriber](https://github.com/lahaxearnaud/simple-text-matcher/blob/master/src/Subscribers/ModelCacheSubscriber.php)
and [StemmerCacheSubscriber](https://github.com/lahaxearnaud/simple-text-matcher/blob/master/src/Subscribers/StemmerCacheSubscriber.php)
are two working examples of cache implementation.
You can use those subscribers or, if you want to store in an other cache provider you can implement your own subscriber.

Events
----

| Event name    | Description |
| ------------- | ------------- |
| alahaxe\SimpleTextMatcher\Events\EngineBuildedEvent::class  | This event is trigger when the engine is builded, at the end of the constructor  |
| alahaxe\SimpleTextMatcher\Events\EngineStartedEvent::class  | This event is triggered when models are builded/loaded and the engine is ready to classify  |
| alahaxe\SimpleTextMatcher\Events\MessageClassifiedEvent::class  | This event is triggered when a message is classified |
| alahaxe\SimpleTextMatcher\Events\MessageCorrectedEvent::class  | This event is triggered when a message is normalized, after all normalizers are executed  |
| alahaxe\SimpleTextMatcher\Events\MessageReceivedEvent::class  | This event is trigger when the engine is builded, at the end of the constructor  |
| alahaxe\SimpleTextMatcher\Events\MessageReceivedEvent::class  | This event is triggered when a message is send to the engine, before all alteration/classification |
| alahaxe\SimpleTextMatcher\Events\ModelExpandedEvent::class  | This event is triggered when all synonymes are applied to the training data |
| alahaxe\SimpleTextMatcher\Events\BeforeModelBuildEvent::class  | This event is trigger before the model builder start building |
| alahaxe\SimpleTextMatcher\Events\EntitiesExtractedEvent::class  | This event is triggered when all data extractor are executed and the result is set on the message |


Create custom classifier
----

You can create your custom classifier by implementing ``TrainingInterface``.

```php
<?php

namespace alahaxe\SimpleTextMatcher\Classifiers;

use alahaxe\SimpleTextMatcher\Stemmer;

class MyCustomeClassifier implements TrainingInterface
{

    /**
     * @var Stemmer
     */
    protected $stemmer;

    /**
     * @var mixed
     * 
     * simple array or special object depending of your Algorithm
     */
    protected $model;
    
    /**
     * @param string $question
     * @return ClassificationResultsBag
     */
    public function classify(string $question): ClassificationResultsBag
    {
        $startTimer = microtime(true);
        // it's recommanded to stem the user question but it's not mandatory
        $question = $this->stemmer->stemPhrase($question);
        $bag = new ClassificationResultsBag();

        // apply you algorithm and add some ClassificationResult in the bag if you match something
        
        $bag->setExecutionTime(microtime(true) - $startTimer);
        return $bag;
    }

    /**
     * @return Stemmer
     */
    public function getStemmer(): Stemmer
    {
        return $this->stemmer;
    }

    /**
     * @param Stemmer $stemmer
     * @return ClassifierInterface
     */
    public function setStemmer(Stemmer $stemmer): ClassifierInterface
    {
        $this->stemmer = $stemmer;
    }

    /**
     * @param array $trainingData
     */
    public function prepareModel(array $trainingData = []): void
    {
        // if you need to rework on the $trainingData it's here
        // you may also need to build a business model (some nlp model for example)
        
        $this->model = $trainingData;
    }

    /**
     * @return mixed
     */
    public function exportModel()
    {
        // the model in a json_serializable format (array, string,...) 
        // if it's an object you can serialize the object
        return $this->model;
    }

    /**
     * @param mixed $modelData
     */
    public function reloadModel($modelData): void
    {
        // the reverse process of self::exportModel
        $this->model = $modelData;
    }
}
``` 

Create custom normalizer
----

If you have custom rules or text correction

```php
<?php

namespace alahaxe\SimpleTextMatcher\Normalizers;

class MyCustomNormalizer implements NormalizerInterface
{
    /**
     * @param string $rawText
     *
     * @return string
     */
    public function normalize(string $rawText): string
    {
        return trim(str_replace(['foo', "bar"], ' ', $rawText));
    }

    /**
     * Priority the biggest will be the first to be applied
     *
     * @return int
     */
    public function getPriority(): int
    {
        return 250; // O (last) to 250 (first)
    }
}
```

License
----

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
