<?php

namespace App\Enum;

/**
 * RepairWindmillSectionEnum class.
 *
 * @category Enum
 */
class RepairWindmillSectionEnum
{
    const BLADE = 0;
    const NACELLE = 1;
    const TOWER = 2;

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
            self::BLADE => 'enum.repair_windmill_section.blade',
            self::NACELLE => 'enum.repair_windmill_section.nacelle',
            self::TOWER => 'enum.repair_windmill_section.tower',
        );
    }

    /**
     * @return array
     */
    public static function getTranslatedReversedEnumArray()
    {
        return array(
            self::BLADE => 'PALA',
            self::NACELLE => 'NACELLE',
            self::TOWER => 'TORRE',
        );
    }

    /**
     * @return array
     */
    public static function getDatagridFilterEnumArray()
    {
        return array(
            'enum.repair_windmill_section.blade' => '0',
            'enum.repair_windmill_section.nacelle' => '1',
            'enum.repair_windmill_section.tower' => '2',
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
        return self::getTranslatedReversedEnumArray()[$type];
    }
}
