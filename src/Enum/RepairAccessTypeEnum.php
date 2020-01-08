<?php

namespace App\Enum;

/**
 * RepairAccessTypeEnum class.
 *
 * @category Enum
 */
class RepairAccessTypeEnum
{
    const CRANE = 0;
    const BASKET_CRANE = 1;
    const ROPES = 2;
    const GROUND = 3;

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
            self::CRANE => 'enum.repair_access_type.crane',
            self::BASKET_CRANE => 'enum.repair_access_type.basket_crane',
            self::ROPES => 'enum.repair_access_type.ropes',
            self::GROUND => 'enum.repair_access_type.ground',
        );
    }

    /**
     * @return array
     */
    public static function getDatagridFilterEnumArray()
    {
        return array(
            'enum.repair_access_type.crane' => '0',
            'enum.repair_access_type.basket_crane' => '1',
            'enum.repair_access_type.ropes' => '2',
            'enum.repair_access_type.ground' => '3',
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
}
