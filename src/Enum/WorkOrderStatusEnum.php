<?php

namespace App\Enum;

/**
 * WorkOrderStatusEnum class.
 *
 * @category Enum
 */
class WorkOrderStatusEnum
{
    const PENDING = 0;
    const DOING = 1;
    const DONE = 2;

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
            'enum.audit_status.pending' => self::PENDING,
            'enum.audit_status.doing' => self::DOING,
            'enum.audit_status.done' => self::DONE,
        );
    }
}
