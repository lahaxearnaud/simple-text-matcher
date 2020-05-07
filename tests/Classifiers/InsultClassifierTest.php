<?php

namespace Alahaxe\SimpleTextMatcher\Tests\Classifiers;

use Alahaxe\SimpleTextMatcher\Classifiers\InsultClassifier;
use Alahaxe\SimpleTextMatcher\Handlers\AbstractHandler;
use Alahaxe\SimpleTextMatcher\Message;
use Alahaxe\SimpleTextMatcher\MessageFlags\Detectors\InsultFlagDetector;
use Alahaxe\SimpleTextMatcher\MessageFlags\Flag;
use Alahaxe\SimpleTextMatcher\Stemmer;
use PHPUnit\Framework\TestCase;

/**
 * Class InsultClassifierTest
 *
 * @package Alahaxe\SimpleTextMatcher\Tests\Classifiers
 */
class InsultClassifierTest extends TestCase
{

    public function testMatchWithFlag()
    {
        $message = new Message('dormir a l hotel batard');
        $message->getFlags()->add(new Flag(InsultFlagDetector::getFlagName()));
        $classifier = new InsultClassifier();
        $result = $classifier->classify($message);
        $this->assertGreaterThan(0, $result->count());
        $this->assertEquals(AbstractHandler::INSULT_INTENT_NAME, $result->first()->getIntent());
    }

    public function testMatchWithoutFlag()
    {
        $message = new Message('dormir a l hotel');
        $classifier = new InsultClassifier();
        $classifier->setStemmer(new Stemmer());
        $result = $classifier->classify($message);
        $this->assertEquals(0, $result->count());
    }
}
