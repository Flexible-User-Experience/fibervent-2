<?php

namespace App\Enum;

use App\Entity\BladeDamage;

/**
 * Class BladeDamageEdgeEnum.
 *
 * @category Enum
 */
class BladeDamageEdgeEnum
{
    const EDGE_IN = 0;
    const EDGE_OUT = 1;
    const EDGE_UNDEFINED = 2;

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
            self::EDGE_IN => 'BA',
            self::EDGE_OUT => 'BS',
            self::EDGE_UNDEFINED => '--',
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
            self::EDGE_IN => 'Atac',
            self::EDGE_OUT => 'Sortida',
            self::EDGE_UNDEFINED => 'No',
        );
    }

    /**
     * @param BladeDamage $bladeDamage
     *
     * @return string
     */
    public static function getStringValue(BladeDamage $bladeDamage)
    {
        return self::getReversedEnumArray()[$bladeDamage->getEdge()];
    }
}
