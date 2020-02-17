<?php

namespace App\Form\Type;

/**
 * UserOperatorChooseYearAndMonthWorkerTimesheet class.
 *
 * @category FormType
 */
class UserOperatorChooseYearAndMonthWorkerTimesheet extends UserOperatorChooseYearAndMonthPresenceMonitoring
{
    /**
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'user_operator_choose_worker_timesheet_period';
    }
}
