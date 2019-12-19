<?php

namespace App\Enum;

use App\Entity\Audit;

/**
 * Class AuditTypeEnum.
 *
 * @category Enum
 */
class AuditTypeEnum
{
    const GROUND = 0;
    const ROPE = 1;
    const INSIDE = 2;
    const PLATFORM = 3;

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
            self::GROUND => 'enum.audit_type.ground',
            self::ROPE => 'enum.audit_type.rope',
            self::INSIDE => 'enum.audit_type.inside',
            self::PLATFORM => 'enum.audit_type.platform',
        );
    }

    /**
     * @param Audit $audit
     *
     * @return string
     */
    public static function getStringValue(Audit $audit)
    {
        return self::getReversedEnumArray()[$audit->getType()];
    }

    /**
     * @param Audit $audit
     *
     * @return string
     */
    public static function getStringLocalizedValue(Audit $audit)
    {
        return self::getReversedEnumArray()[$audit->getType()];
    }
}
