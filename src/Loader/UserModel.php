<?php


namespace Alahaxe\SimpleTextMatcher\Loader;


use Alahaxe\SimpleTextMatcher\Handlers\AbstractHandler;

class UserModel
{
    /**
     * @var array
     */
    protected $training;

    /**
     * @var array
     */
    protected $synonyms;

    /**
     * @var array
     */
    protected $intentExtractors;

    /**
     * @var AbstractHandler[]
     */
    protected $intentHandlers;

    /**
     * UserModel constructor.
     * @param array $training
     * @param array $synonyms
     * @param array $intentExtractors
     * @param AbstractHandler[] $intentHandlers
     */
    public function __construct(array $training, array $synonyms, array $intentExtractors, array $intentHandlers)
    {
        $this->training = $training;
        $this->synonyms = $synonyms;
        $this->intentExtractors = $intentExtractors;
        $this->intentHandlers = $intentHandlers;
    }

    /**
     * @return array
     */
    public function getTraining(): array
    {
        return $this->training;
    }

    /**
     * @return array
     */
    public function getSynonyms(): array
    {
        return $this->synonyms;
    }

    /**
     * @return array
     */
    public function getIntentExtractors(): array
    {
        return $this->intentExtractors;
    }

    /**
     * @return AbstractHandler[]
     */
    public function getIntentHandlers(): array
    {
        return $this->intentHandlers;
    }
}
