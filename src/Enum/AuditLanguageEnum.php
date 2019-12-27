<?php

namespace App\Enum;

/**
 * Class AuditLanguageEnum.
 *
 * @category Enum
 */
class AuditLanguageEnum
{
    const SPANISH = 0;
    const ENGLISH = 1;
    const FRENCH = 2;
    const PORTUGUESE = 3;
    const GERMAN = 4;
    const ITALIAN = 5;

    /**
     * Methods.
     */

    /**
     * @return array
     */
    public static function getEnumArray()
    {
        return array_flip(self::getReversedEnumArray());
    }

    /**
     * @return array
     */
    public static function getReversedEnumArray()
    {
        return array(
            self::SPANISH => 'es',
            self::ENGLISH => 'en',
            self::FRENCH => 'fr',
            self::PORTUGUESE => 'pt',
            self::GERMAN => 'de',
            self::ITALIAN => 'it',
        );
    }

    /**
     * @return array
     */
    public static function getEnumArrayString()
    {
        return array_flip(self::getReversedEnumArrayString());
    }

    /**
     * @return array
     */
    public static function getReversedEnumArrayString()
    {
        return array(
            self::SPANISH => 'español',
            self::ENGLISH => 'english',
            self::FRENCH => 'français',
            self::PORTUGUESE => 'portuguès',
            self::GERMAN     => 'alemany',
            self::ITALIAN    => 'italià',
        );
    }
}
