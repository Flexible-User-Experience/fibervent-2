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
        return self::getEnumArray()[$deliveryNoteTimeRegister->getType()];
    }
}
