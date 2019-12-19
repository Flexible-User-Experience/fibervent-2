<?php

namespace App\Enum;

/**
 * Class BladeDamageStatusEnum.
 *
 * @category Enum
 */
class BladeDamageStatusEnum
{
    const STATUS_NOT_VALIDATED = 0;
    const STATUS_VALIDATED = 1;

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
            self::STATUS_NOT_VALIDATED => 'No validat',
            self::STATUS_VALIDATED => 'Validat',
        );
    }
}
