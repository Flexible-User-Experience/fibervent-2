<?php

namespace App\Enum;

/**
 * MinutesEnum class.
 *
 * @category Enum
 */
class MinutesEnum
{
    /**
     * @return array
     */
    public static function getEnumArray()
    {
        return array(
            0,
            5,
            10,
            15,
            20,
            25,
            30,
            35,
            40,
            45,
            50,
            55,
        );
    }

    /**
     * @return array
     */
    public static function getQuartersEnumArray()
    {
        return array(
            0,
            15,
            30,
            45,
        );
    }

    /**
     * @return array
     */
    public static function getChoicesQuartersEnumArray()
    {
        return array(
            '0h' => 0.0,
            '15m' => 0.25,
            '30m' => 0.50,
            '45m' => 0.75,
            '1h' => 1.0,
            '1h 15m' => 1.25,
            '1h 30m' => 1.50,
            '1h 45m' => 1.75,
            '2h' => 2.0,
            '2h 15m' => 2.25,
            '2h 30m' => 2.50,
            '2h 45m' => 2.75,
            '3h' => 3.0,
            '3h 15m' => 3.25,
            '3h 30m' => 3.50,
            '3h 45m' => 3.75,
            '4h' => 4.0,
            '4h 15m' => 4.25,
            '4h 30m' => 4.50,
            '4h 45m' => 4.75,
            '5h' => 5.0,
            '5h 15m' => 5.25,
            '5h 30m' => 5.50,
            '5h 45m' => 5.75,
            '6h' => 6.0,
            '6h 15m' => 6.25,
            '6h 30m' => 6.50,
            '6h 45m' => 6.75,
            '7h' => 7.0,
            '7h 15m' => 7.25,
            '7h 30m' => 7.50,
            '7h 45m' => 7.75,
            '8h' => 8.0,
            '8h 15m' => 8.25,
            '8h 30m' => 8.50,
            '8h 45m' => 8.75,
            '9h' => 9.0,
            '9h 15m' => 9.25,
            '9h 30m' => 9.50,
            '9h 45m' => 9.75,
            '10h' => 10.0,
            '10h 15m' => 10.25,
            '10h 30m' => 10.50,
            '10h 45m' => 10.75,
            '11h' => 11.0,
            '11h 15m' => 11.25,
            '11h 30m' => 11.50,
            '11h 45m' => 11.75,
            '12h' => 12.0,
            '12h 15m' => 12.25,
            '12h 30m' => 12.50,
            '12h 45m' => 12.75,
            '13h' => 13.0,
            '13h 15m' => 13.25,
            '13h 30m' => 13.50,
            '13h 45m' => 13.75,
            '14h' => 14.0,
        );
    }
}
