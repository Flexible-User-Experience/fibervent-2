<?php

namespace App\Manager;

use App\Entity\PresenceMonitoring;
use App\Entity\User;
use App\Enum\PresenceMonitoringCategoryEnum;
use App\Repository\PresenceMonitoringRepository;

/**
 * Class PresenceMonitoringManager
 *
 * @category Manager
 */
class PresenceMonitoringManager
{
    /**
     * @var PresenceMonitoringRepository $pmr
     */
    private PresenceMonitoringRepository $pmr;

    /**
     * Methods.
     */

    /**
     * WorkOrderManager constructor.
     *
     * @param PresenceMonitoringRepository $pmr
     */
    public function __construct(PresenceMonitoringRepository $pmr)
    {
        $this->pmr = $pmr;
    }

    /**
     * @param User $operator
     * @param int  $year
     * @param int  $month
     *
     * @return array|PresenceMonitoring[]
     */
    public function getItemsByOperatorYearAndMonthSortedByDate(User $operator, $year, $month)
    {
        return $this->pmr->findByOperatorYearAndMonthSortedByDate($operator, $year, $month);
    }

    /**
     * @param User $operator
     * @param int $year
     * @param int $month
     *
     * @return array|PresenceMonitoring[]
     * @throws \Exception
     */
    public function createFullMonthItemsListByOperatorYearAndMonth(User $operator, $year, $month)
    {
        $result = array();
        $items = $this->getItemsByOperatorYearAndMonthSortedByDate($operator, $year, $month);
        $currentItem = array_shift($items);
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
        for ($dayNumber = 1; $dayNumber <= $daysInMonth; $dayNumber++) {
            $day = new \DateTime();
            $day->setDate($year, $month, $dayNumber);
            if ($currentItem && $currentItem->getDateString() == $day->format('d/m/Y')) {
                array_push($result, $currentItem);
                $currentItem = array_shift($items);
            } else {
                array_push($result, $this->buildEmptyItemForDay($operator, $day));
            }
        }

        return $result;
    }

    /**
     * @param User $operator
     * @param \DateTime $day
     *
     * @return PresenceMonitoring
     */
    private function buildEmptyItemForDay(User $operator, \DateTime $day)
    {
        $emptyItem = new PresenceMonitoring();
        $emptyItem
            ->setWorker($operator)
            ->setDate($day)
            ->setCategory(PresenceMonitoringCategoryEnum::WORKDAY)
        ;

        return $emptyItem;
    }
}
