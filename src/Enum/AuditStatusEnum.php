<?php

namespace App\Enum;

/**
 * AuditStatusEnum class.
 *
 * @category Enum
 */
class AuditStatusEnum extends WorkOrderStatusEnum
{
    const INVOICED = 3;

    /**
     * Methods.
     */

    /**
     * @return array
     */
    public static function getReversedEnumArray()
    {
        $result = parent::getReversedEnumArray();
        $result['enum.audit_status.invoiced'] = self::INVOICED;

        return $result;
    }
}
