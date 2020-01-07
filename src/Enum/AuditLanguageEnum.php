<?php

namespace App\Enum;

/**
 * Class AuditLanguageEnum.
 *
 * @category Enum
 */
class AuditLanguageEnum
{
    const DEFAULT_LANGUAGE_STRING = 'es';

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
            self::SPANISH => self::DEFAULT_LANGUAGE_STRING,
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
            self::ENGLISH => 'inglés',
            self::FRENCH => 'francés',
            self::PORTUGUESE => 'portugués',
            self::GERMAN     => 'aleman',
            self::ITALIAN    => 'italiano',
        );
    }
}
