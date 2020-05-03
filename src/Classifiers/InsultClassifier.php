<?php

namespace Alahaxe\SimpleTextMatcher\Classifiers;

use Alahaxe\SimpleTextMatcher\Handlers\AbstractHandler;
use Alahaxe\SimpleTextMatcher\Message;
use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\InsultFlagDetector;
use Alahaxe\SimpleTextMatcher\Stemmer;

/**
 * Class InsultClassifier
 *
 * @package Alahaxe\SimpleTextMatcher\Classifiers
 */
class InsultClassifier implements ClassifierInterface
{
    /**
     * @param Message $question
     * @return ClassificationResultsBag
     */
    public function classify(Message $question): ClassificationResultsBag
    {
        $bag = new ClassificationResultsBag();
        $startTimer = microtime(true);

        if ($question->getFlags()->hasFlag(InsultFlagDetector::getFlagName())) {
            $bag->add(new ClassificationResult(__CLASS__, AbstractHandler::INSULT_INTENT_NAME,  1));
        }
        $bag->setExecutionTime(microtime(true) - $startTimer);

        return $bag;
    }

    /**
     * @param Stemmer $stemmer
     * @return ClassifierInterface
     */
    public function setStemmer(Stemmer $stemmer): ClassifierInterface
    {
        return $this;
    }
}
