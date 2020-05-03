<?php


namespace Alahaxe\SimpleTextMatcher\Loader;

/**
 * Interface LoaderInterface
 *
 * @package Alahaxe\SimpleTextMatcher\Loader
 */
interface LoaderInterface
{

    /**
     * @return UserModel
     * @throws LoaderException
     */
    public function load():UserModel;
}
