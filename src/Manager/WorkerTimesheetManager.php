<?php

namespace App\Manager;

use App\Entity\WorkerTimesheet;
use App\Entity\User;
use App\Repository\WorkerTimesheetRepository;

/**
 * Class WorkerTimesheetManager
 *
 * @category Manager
 */
class WorkerTimesheetManager
{
    /**
     * @var WorkerTimesheetRepository $wtr
     */
    private WorkerTimesheetRepository $wtr;

    /**
     * Methods.
     */

    /**
     * WorkerTimesheetManager constructor.
     *
     * @param WorkerTimesheetRepository $wtr
     */
    public function __construct(WorkerTimesheetRepository $wtr)
    {
        $this->wtr = $wtr;
    }

    /**
     * @param User $operator
     * @param int  $year
     * @param int  $month
     *
     * @return array|WorkerTimesheet[]
     */
    public function getAllItemsByOperatorYearAndMonthSortedByDate(User $operator, $year, $month)
    {
        return $this->wtr->findAllDaysByOperatorYearAndMonthSortedByDate($operator, $year, $month);
    }

    /**
     * @param User $operator
     * @param int $year
     * @param int $month
     *
     * @return array|WorkerTimesheet[]
     */
    public function createFullMonthItemsListByOperatorYearAndMonth(User $operator, $year, $month)
    {
        $items = $this->getAllItemsByOperatorYearAndMonthSortedByDate($operator, $year, $month);
        $totalItem = $this->buildEmptyItemForDay($operator);
        /** @var WorkerTimesheet $item */
        foreach ($items as $item) {
            $totalItem
                ->setTotalNormalHours($totalItem->getTotalNormalHours() + $item->getTotalNormalHours())
                ->setTotalVerticalHours($totalItem->getTotalVerticalHours() + $item->getTotalVerticalHours())
                ->setTotalInclementWeatherHours($totalItem->getTotalInclementWeatherHours() + $item->getTotalInclementWeatherHours())
                ->setTotalTripHours($totalItem->getTotalTripHours() + $item->getTotalTripHours())
            ;
        }
        array_push($items, $totalItem);

        return $items;
    }

    /**
     * @param User $operator
     *
     * @return WorkerTimesheet
     */
    private function buildEmptyItemForDay(User $operator)
    {
        $emptyItem = new WorkerTimesheet();
        $emptyItem
            ->setWorker($operator)
            ->setTotalNormalHours(0)
            ->setTotalVerticalHours(0)
            ->setTotalInclementWeatherHours(0)
            ->setTotalTripHours(0)
        ;

        return $emptyItem;
    }
}
