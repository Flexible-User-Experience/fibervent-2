<?php

namespace App\Enum;

/**
 * Class BladeEnum.
 *
 * @category Enum
 */
class BladeEnum
{
    const BLADE_1 = 1;
    const BLADE_2 = 2;
    const BLADE_3 = 3;

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
            self::BLADE_1 => '1',
            self::BLADE_2 => '2',
            self::BLADE_3 => '3',
        );
    }

    /**
     * @return array
     */
    public static function getLongTextEnumArray()
    {
        return array_flip(self::getReversedLongTextEnumArray());
    }

    /**
     * @return array
     */
    public static function getReversedLongTextEnumArray()
    {
        return array(
            self::BLADE_1 => 'A',
            self::BLADE_2 => 'B',
            self::BLADE_3 => 'C',
        );
    }

    /**
     * @param int $type
     *
     * @return string
     */
    public static function getDecodedStringFromType(int $type)
    {
        return self::getReversedEnumArray()[$type];
    }

    /**
     * @param int $type
     *
     * @return string
     */
    public static function getTranslatedDecodedStringFromType(int $type)
    {
        return self::getReversedEnumArray()[$type];
    }
}
