<?php

namespace alahaxe\SimpleTextMatcher\Classifiers;

/**
 * Interface TrainningInterface
 *
 * @package alahaxe\SimpleTextMatcher\Classifiers
 */
interface TrainingInterface extends ClassifierInterface
{
    /**
     * @param array $trainingData
     */
    public function prepareModel(array $trainingData = []):void;

    /**
     * @return mixed
     */
    public function exportModel();

    /**
     * @param mixed $modelData
     */
    public function reloadModel($modelData):void;
}
