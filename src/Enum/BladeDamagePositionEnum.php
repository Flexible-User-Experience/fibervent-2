<?php

namespace App\Enum;

use App\Entity\BladeDamage;
use App\Entity\WorkOrderTask;

/**
 * Class BladeDamagePositionEnum.
 *
 * @category Enum
 */
class BladeDamagePositionEnum
{
    const VALVE_PRESSURE = 0;
    const VALVE_SUCTION = 1;
    const EDGE_IN = 2;
    const EDGE_OUT = 3;

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
            self::VALVE_PRESSURE => 'VP',
            self::VALVE_SUCTION => 'VS',
            self::EDGE_IN => 'BA',
            self::EDGE_OUT => 'BS',
        );
    }

    /**
     * @return array
     */
    public static function getLocalizedEnumArray()
    {
        return array_flip(self::getReversedLocalizedEnumArray());
    }

    /**
     * @return array
     */
    public static function getReversedLocalizedEnumArray()
    {
        return array(
            self::VALVE_PRESSURE => 'enum.blade_damage_position.1_VP',
            self::VALVE_SUCTION => 'enum.blade_damage_position.2_VS',
            self::EDGE_IN => 'enum.blade_damage_position.3_BA',
            self::EDGE_OUT => 'enum.blade_damage_position.4_BS',
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
            self::VALVE_PRESSURE => 'Valva pressió',
            self::VALVE_SUCTION => 'Valva succió',
            self::EDGE_IN => 'Vora atac',
            self::EDGE_OUT => 'Vora sortida',
        );
    }

    /**
     * @param BladeDamage|WorkOrderTask $bladeDamage
     *
     * @return string
     */
    public static function getStringValue($bladeDamage)
    {
        return self::getReversedEnumArray()[$bladeDamage->getPosition()];
    }

    /**
     * @param BladeDamage|WorkOrderTask $bladeDamage
     *
     * @return string
     */
    public static function getStringLocalizedValue($bladeDamage)
    {
        return self::getReversedLocalizedEnumArray()[$bladeDamage->getPosition()];
    }
}
