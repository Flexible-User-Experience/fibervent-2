<?php

namespace App\Enum;

/**
 * MonthsEnum class.
 *
 * @category Enum
 */
class MonthsEnum
{
    const JANUARY = 1;
    const FEBRAURY = 2;
    const MARCH = 3;
    const APRIL = 4;
    const MAY = 5;
    const JUNE = 6;
    const JULY = 7;
    const AUGUST = 8;
    const SEPTEMBER = 9;
    const OCTOBER = 10;
    const NOVEMBER = 11;
    const DECEMBER = 12;

    /**
     * @return array
     */
    public static function getMonthEnumArray()
    {
        return array_flip(self::getOldMonthEnumArray());
    }

    /**
     * @return array
     */
    public static function getOldMonthEnumArray()
    {
        return array(
            self::JANUARY => 'month.january',
            self::FEBRAURY => 'month.febraury',
            self::MARCH => 'month.march',
            self::APRIL => 'month.april',
            self::MAY => 'month.may',
            self::JUNE => 'month.june',
            self::JULY => 'month.july',
            self::AUGUST => 'month.august',
            self::SEPTEMBER => 'month.september',
            self::OCTOBER => 'month.october',
            self::NOVEMBER => 'month.november',
            self::DECEMBER => 'month.december',
        );
    }
}
