<?php

namespace App\Enum;

use App\Entity\DeliveryNoteTimeRegister;

/**
 * TimeRegisterTypeEnum class.
 *
 * @category Enum
 */
class TimeRegisterTypeEnum
{
    const TRIP = 0;
    const STOP = 1;
    const WORK = 2;

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
            self::TRIP => 'enum.time_register_type.trip',
            self::STOP => 'enum.time_register_type.stop',
            self::WORK => 'enum.time_register_type.work',
        );
    }

    /**
     * @param DeliveryNoteTimeRegister $deliveryNoteTimeRegister
     *
     * @return string
     */
    public static function getStringValue(DeliveryNoteTimeRegister $deliveryNoteTimeRegister)
    {
        return self::getReversedEnumArray()[$deliveryNoteTimeRegister->getType()];
    }
}
