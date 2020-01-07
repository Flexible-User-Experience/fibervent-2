<?php

namespace App\Manager;

use App\Entity\PresenceMonitoring;
use App\Entity\User;
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
        $items = $this->getItemsByOperatorYearAndMonthSortedByDate($operator, $year, $month);
        $emptyItem = new PresenceMonitoring();
        $emptyItem
            ->setDate(new \DateTime())
            ->setWorker($operator)
            ->setTotalHours(3)
        ;
        array_push($items, $emptyItem);
        $fullYearMonthCalendarItems = array();

        return $items;
    }
}
