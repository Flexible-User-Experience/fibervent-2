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
            '14h 15m' => 14.25,
            '14h 30m' => 14.50,
            '14h 45m' => 14.75,
            '15h' => 15.0,
            '15h 15m' => 15.25,
            '15h 30m' => 15.50,
            '15h 45m' => 15.75,
            '16h' => 16.0,
            '16h 15m' => 16.25,
            '16h 30m' => 16.50,
            '16h 45m' => 16.75,
            '17h' => 17.0,
            '17h 15m' => 17.25,
            '17h 30m' => 17.50,
            '17h 45m' => 17.75,
            '18h' => 18.0,
            '18h 15m' => 18.25,
            '18h 30m' => 18.50,
            '18h 45m' => 18.75,
            '19h' => 19.0,
            '19h 15m' => 19.25,
            '19h 30m' => 19.50,
            '19h 45m' => 19.75,
            '20h' => 20.0,
        );
    }

    /**
     * Get an hours float value and transform to a string with time ellapsed
     *
     * @param float $value
     *
     * @return string
     */
    public static function transformToHoursAmountString(float $value)
    {
        $result = '0h';
        $whole = floor($value);
        $fraction = $value - $whole;
        if ($whole > 0.0) {
            $result = $whole.'h';
        }
        if ($fraction > 0.0) {
            if ($whole == 0.0) {
                $result = self::transformMinutesAmountString($fraction);
            } else {
                $result .= ' '.self::transformMinutesAmountString($fraction);
            }
        }

        return $result;
    }

    /**
     * @param float $value
     *
     * @return string
     */
    private static function transformMinutesAmountString(float $value)
    {
        $result = '';
        if ($value == 0.25) {
            $result = '15m';
        } elseif ($value == 0.50) {
            $result = '30m';
        } elseif ($value == 0.75) {
            $result = '45m';
        }

        return $result;
    }
}
