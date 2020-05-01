<?php

namespace Alahaxe\SimpleTextMatcher\Entities\Extractors\Dictionnary;

use Alahaxe\SimpleTextMatcher\Entities\Entity;
use Alahaxe\SimpleTextMatcher\Entities\EntityBag;
use Alahaxe\SimpleTextMatcher\Entities\EntityExtractorInterface;
use Alahaxe\SimpleTextMatcher\Normalizers\LowerCaseNormalizer;
use Alahaxe\SimpleTextMatcher\Normalizers\NormalizersBag;
use Alahaxe\SimpleTextMatcher\Normalizers\UnaccentNormalizer;
use Alahaxe\SimpleTextMatcher\StringUtils;

/**
 *
 * @package Alahaxe\SimpleTextMatcher\Entities
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
        $words = StringUtils::words($question);
        $handle = fopen($this->dataFilePath, "r");

        $orderedEntities = [];
        while (($value = fgets($handle)) !== false) {
            $value = $this->normalizeDictionaryValue($value);

            if ($index = $this->testValue($value, $words, $question)) {
                $orderedEntities[$index] = new Entity($this->getTypeExtracted(), $this->normalizeValue($value));
            }
        }
        fclose($handle);

        for ($i = 0; $i < count($words); $i++) {
            if (!isset($orderedEntities[$i])) {
                continue;
            }

            $result->add($orderedEntities[$i]);
        }

        return $result;
    }

    /**
     * @param string $dictionaryValue
     * @param array $words
     * @param string $question
     *
     * @return int|bool (index in words or true)
     */
    public function testValue(string $dictionaryValue, array $words, string $question)
    {
        if (strpos($dictionaryValue, ' ') === false) {
            return array_search($dictionaryValue, $words, true);
        }

        return strpos($question, $dictionaryValue) !== false;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function normalizeDictionaryValue(string $value):string
    {
        return trim($value);
    }

    /**
     * @param string $rawValue
     * @return string
     */
    public function normalizeValue(string $rawValue):string
    {
        return $rawValue;
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
