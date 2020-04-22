<?php

namespace Alahaxe\SimpleTextMatcher\Entities\Extractors\Regex;

use Alahaxe\SimpleTextMatcher\Entities\EntityBag;

/**
 * Class CreditCardExtractor
 * @package Alahaxe\SimpleTextMatcher\Entities
 */
class CreditCardExtractor extends AbstractRegexExtractor
{

    /**
     * @inheritDoc
     */
    public function extract(string $question): EntityBag
    {
        return parent::extract(str_replace([' ', '-'], '', $question));
    }

    /**
     * @return array
     */
    public function getRegexes(): array
    {
        $brandsRegexes = [
            '(4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14})', //Visa Master Card
            '(5[1-5][0-9]{14})',                  // MasterCard
            '4[0-9]{12}(?:[0-9]{3})?',         // Visa
            '65[4-9][0-9]{13}|64[4-9][0-9]{13}|6011[0-9]{12}|(622(?:12[6-9]|1[3-9][0-9]|[2-8][0-9][0-9]|9[01][0-9]|92[0-5])[0-9]{10})',      // Discover
            '(3[47][0-9]{13})',                   // AMEX
            '(3(?:0[0-5]|[68][0-9])[0-9]{11})',   // Diners Club
            '((?:2131|1800|35[0-9]{3})[0-9]{11})',  // JCB
            '(6541|6556)[0-9]{12}', // BCGlobal
            '389[0-9]{11}', // Carte Blanche Card
            '63[7-9][0-9]{13}', // Insta Payment Card
            '(?:2131|1800|35\d{3})\d{11}', //JCB Card
            '9[0-9]{15}', //KoreanLocalCard
            '(6304|6706|6709|6771)[0-9]{12,15}', // Laser Card
            '(5018|5020|5038|6304|6759|6761|6763)[0-9]{8,15}', // Maestro Card
            '(6334|6767)[0-9]{12}|(6334|6767)[0-9]{14}|(6334|6767)[0-9]{15}', // Solo Card
            '(4903|4905|4911|4936|6333|6759)[0-9]{12}|(4903|4905|4911|4936|6333|6759)[0-9]{14}|(4903|4905|4911|4936|6333|6759)[0-9]{15}|564182[0-9]{10}|564182[0-9]{12}|564182[0-9]{13}|633110[0-9]{10}|633110[0-9]{12}|633110[0-9]{13}', // Switch Card
            '(62[0-9]{14,17})', // Union Pay Card
        ];

        // merge all regex into a big one
        $brandsRegexes = array_map(static function($brandRegex) {
            return '('.$brandRegex.')';
        }, $brandsRegexes);

        return [
            '/'.implode('|', $brandsRegexes).'/'
        ];
    }

    /**
     * @return string
     */
    public function getTypeExtracted(): string
    {
        return 'CREDIT_CARD';
    }
}
