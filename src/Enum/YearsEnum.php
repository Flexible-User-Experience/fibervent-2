<?php

namespace App\Enum;

/**
 * YearsEnum class.
 *
 * @category Enum
 */
class YearsEnum
{
    const APP_FIRST_YEAR = 2020;

    /**
     * @return array
     *
     * @throws \Exception
     */
    public static function getYearEnumArray()
    {
        $result = array();
        $now = new \DateTimeImmutable();
        $currentYear = intval($now->format('Y'));
        if (12 == intval($now->format('m')) && 15 < intval($now->format('d'))) {
            ++$currentYear;
        }
        $steps = $currentYear - self::APP_FIRST_YEAR + 1;
        for ($i = 0; $i < $steps; ++$i) {
            $year = $currentYear - $i;
            $result["$year"] = $year;
        }

        return $result;
    }
}
