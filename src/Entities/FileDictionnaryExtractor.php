<?php

namespace alahaxe\SimpleTextMatcher\Entities;

use alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use alahaxe\SimpleTextMatcher\Normalizers\SingularizeNormalizer;
use alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;

/**
 * Country list are from https://github.com/umpirsky/country-list/tree/master/data
 *
 * @package alahaxe\SimpleTextMatcher\Entities
 */
abstract class FileDictionnaryExtractor implements EntityExtractorInterface
{
    /**
     * @var string
     */
    protected $dataFilePath;

    /**
     * @var string
     */
    protected $type;

    /**
     * FirstNameExtractor constructor.
     * @param string $dataFilePath
     */
    public function __construct(string $type, string $dataFilePath)
    {
        $this->dataFilePath = $dataFilePath;
        $this->type = $type;
    }

    /**
     * @param string $question
     *
     * @return EntityBag
     */
    public function extract(string $question): EntityBag
    {
        $result = new EntityBag();

        if (!file_exists($this->dataFilePath) || !is_readable($this->dataFilePath)) {
            return $result;
        }

        $normalizers = $this->getNormalizers();
        $question = $normalizers->apply($question);
        $words = explode(' ', $question);
        $handle = fopen($this->dataFilePath, "r");

        $orderedEntities = [];
        while (($value = fgets($handle)) !== false) {
            $value = trim($value);

            if ($index = array_search($value, $words, true)) {
                $orderedEntities[$index] = new Entity($this->getTypeExtracted(), $value);
            }
        }
        fclose($handle);

        for($i = 0; $i < count($words); $i++) {
            if (!isset($orderedEntities[$i])) {
                continue;
            }

            $result->add($orderedEntities[$i]);

        }

        return $result;
    }

    /**
     * @return string
     */
    public function getTypeExtracted(): string
    {
        return $this->type;
    }

    /**
     * @return NormalizersBag
     */
    public function getNormalizers(): NormalizersBag
    {
        $normalizers = new NormalizersBag();
        $normalizers
            ->add(new LowerCaseNormalizer())
            ->add(new UnaccentNormalizer())
        ;

        return $normalizers;
    }
}
