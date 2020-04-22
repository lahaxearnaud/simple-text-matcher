<?php

namespace Alahaxe\SimpleTextMatcher\Events;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event is triggered when all synonymes are applied to the training data
 *
 * @package Alahaxe\SimpleTextMatcher\Events
 */
class ModelExpandedEvent extends Event
{
    /**
     * @var array
     */
    protected $model;

    /**
     * ModelExpendedEvent constructor.
     *
     * @param array $model
     */
    public function __construct(array $model)
    {
        $this->model = $model;
    }

    /**
     * @return array
     */
    public function getModel(): array
    {
        return $this->model;
    }
}
