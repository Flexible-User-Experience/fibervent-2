<?php

namespace App\Enum;

use App\Entity\DeliveryNoteTimeRegister;

/**
 * TimeRegisterShiftEnum class.
 *
 * @category Enum
 */
class TimeRegisterShiftEnum
{
    const MORNING = 0;
    const AFTERNOON = 1;
    const NIGHT = 2;

    /**
     * Methods.
     */

    /**
     * @return array
     */
    public static function getEnumArray()
    {
        return array(
            self::MORNING => 'enum.time_register_shift.morning',
            self::AFTERNOON => 'enum.time_register_shift.afternoon',
            self::NIGHT => 'enum.time_register_shift.night',
        );
    }

    /**
     * @param DeliveryNoteTimeRegister $deliveryNoteTimeRegister
     *
     * @return string
     */
    public static function getStringValue(DeliveryNoteTimeRegister $deliveryNoteTimeRegister)
    {
        return self::getEnumArray()[$deliveryNoteTimeRegister->getShift()];
    }
}
