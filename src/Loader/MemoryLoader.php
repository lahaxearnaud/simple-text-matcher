<?php


namespace Alahaxe\SimpleTextMatcher\Loader;

/**
 * Class MemoryLoader
 *
 * @package Alahaxe\SimpleTextMatcher\Loader
 */
class MemoryLoader extends AbstractLoader
{
    /**
     * @var UserModel
     */
    protected $model;

    /**
     * MemoryLoader constructor.
     * @param UserModel $model
     */
    public function __construct(UserModel $model)
    {
        $this->model = $model;
    }

    public function load(): UserModel
    {
        return $this->model;
    }
}
