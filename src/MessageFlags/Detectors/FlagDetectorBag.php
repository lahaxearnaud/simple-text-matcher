<?php

namespace Alahaxe\SimpleTextMatcher\MessageFlags\Detectors;

use Alahaxe\SimpleTextMatcher\Message;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class FlagDetectorBag
 * @package Alahaxe\SimpleTextMatcher\MessageFlags
 *
 * @template-extends ArrayCollection<int, FlagDetectorInterface>
 */
class FlagDetectorBag extends ArrayCollection
{

    /**
     * @inheritDoc
     * @return  FlagDetectorInterface[]
     */
    public function all()
    {
        return $this->toArray();
    }

    /**
     * @param Message $message
     */
    public function apply(Message $message):void
    {
        /** @var FlagDetectorInterface $flagDetector */
        foreach ($this->toArray() as $flagDetector) {
            if ($flagDetector->detect($message)) {
                $message->addFlag($flagDetector->buildFlag());
            }
        }
    }
}
