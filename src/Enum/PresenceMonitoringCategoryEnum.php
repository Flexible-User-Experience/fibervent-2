<?php

namespace App\Enum;

/**
 * PresenceMonitoringCategoryEnum class.
 *
 * @category Enum
 */
class PresenceMonitoringCategoryEnum
{
    const WORKDAY = 0;
    const DAYOFF = 1;
    const PERMITS = 2;
    const LEAVE = 3;

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
            self::WORKDAY => 'enum.presence_monitoring_category.workday',
            self::DAYOFF => 'enum.presence_monitoring_category.dayoff',
            self::PERMITS => 'enum.presence_monitoring_category.permits',
            self::LEAVE => 'enum.presence_monitoring_category.leave',
        );
    }

    /**
     * @param int $category
     *
     * @return string
     */
    public static function getDecodedString(int $category)
    {
        return self::getReversedEnumArray()[$category];
    }
}
