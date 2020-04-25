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
    use Alahaxe\SimpleTextMatcher\EngineFactory;

    $factory = new EngineFactory();
    $engine = $factory->build();

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
    $message = new \Alahaxe\SimpleTextMatcher\Message($question);

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
| Alahaxe\SimpleTextMatcher\Events\EngineBuildedEvent::class  | This event is trigger when the engine is builded, at the end of the constructor  |
| Alahaxe\SimpleTextMatcher\Events\EngineStartedEvent::class  | This event is triggered when models are builded/loaded and the engine is ready to classify  |
| Alahaxe\SimpleTextMatcher\Events\MessageClassifiedEvent::class  | This event is triggered when a message is classified |
| Alahaxe\SimpleTextMatcher\Events\MessageCorrectedEvent::class  | This event is triggered when a message is normalized, after all normalizers are executed  |
| Alahaxe\SimpleTextMatcher\Events\MessageReceivedEvent::class  | This event is trigger when the engine is builded, at the end of the constructor  |
| Alahaxe\SimpleTextMatcher\Events\MessageReceivedEvent::class  | This event is triggered when a message is send to the engine, before all alteration/classification |
| Alahaxe\SimpleTextMatcher\Events\ModelExpandedEvent::class  | This event is triggered when all synonymes are applied to the training data |
| Alahaxe\SimpleTextMatcher\Events\BeforeModelBuildEvent::class  | This event is trigger before the model builder start building |
| Alahaxe\SimpleTextMatcher\Events\EntitiesExtractedEvent::class  | This event is triggered when all data extractor are executed and the result is set on the message |


Create custom classifier
----

You can create your custom classifier by implementing ``TrainingInterface``.

```php
<?php

namespace Alahaxe\SimpleTextMatcher\Classifiers;

use Alahaxe\SimpleTextMatcher\Stemmer;

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

namespace Alahaxe\SimpleTextMatcher\Normalizers;

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
