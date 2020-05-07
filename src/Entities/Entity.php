<?php


namespace Alahaxe\SimpleTextMatcher\Entities;

/**
 * Class Entity
 * @package Alahaxe\SimpleTextMatcher\Entities
 */
class Entity implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * Entity constructor.
     * @param string $type
     * @param mixed $value
     * @param string $name
     */
    public function __construct(string $type, $value, string $name = null)
    {
        $this->type = $type;
        $this->value = $value;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return Entity
     */
    public function setValue($value): Entity
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Entity
     */
    public function setName(?string $name): Entity
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
            'name' => $this->name,
        ];
    }
}
