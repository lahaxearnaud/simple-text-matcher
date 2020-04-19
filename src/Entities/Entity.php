<?php


namespace alahaxe\SimpleTextMatcher\Entities;

/**
 * Class Entity
 * @package alahaxe\SimpleTextMatcher\Entities
 */
class Entity
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $value;

    /**
     * Entity constructor.
     * @param string $type
     * @param string $value
     */
    public function __construct(string $type, string $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
