<?php

namespace Alahaxe\SimpleTextMatcher\MessageFlags;

/**
 * Class Flag
 *
 * @package Alahaxe\SimpleTextMatcher\MessageFlags
 */
class Flag implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $name;

    /**
     * Flag constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
