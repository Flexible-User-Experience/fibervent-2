<?php

namespace App\Manager;

use App\Entity\DeliveryNote;
use App\Entity\DeliveryNoteTimeRegister;
use App\Enum\TimeRegisterShiftEnum;
use App\Enum\TimeRegisterTypeEnum;
use App\Repository\DeliveryNoteTimeRegisterRepository;
use Exception;

/**
 * Class DeliveryNoteTimeRegisterManager
 *
 * @category Manager
 */
class DeliveryNoteTimeRegisterManager
{
    /**
     * @var DeliveryNoteTimeRegisterRepository
     */
    private DeliveryNoteTimeRegisterRepository $dntrr;

    /**
     * Methods
     */

    /**
     * DeliveryNoteTimeRegisterManager constructor.
     *
     * @param DeliveryNoteTimeRegisterRepository $dntrr
     */
    public function __construct(DeliveryNoteTimeRegisterRepository $dntrr)
    {
        $this->dntrr = $dntrr;
    }

    /**
     * @param DeliveryNote $dn
     *
     * @return DeliveryNoteTimeRegister[]|array
     * @throws Exception
     */
    public function getDeliveryNoteTimeRegistersSortedAndFormatedArray(DeliveryNote $dn)
    {
        $result = array();
        $result[TimeRegisterShiftEnum::MORNING][TimeRegisterTypeEnum::TRIP] = $this->dntrr->getMorningTripsFromDeliveryNoteSortedByTime($dn);
        $result[TimeRegisterShiftEnum::AFTERNOON][TimeRegisterTypeEnum::TRIP] = $this->dntrr->getAfternoonTripsFromDeliveryNoteSortedByTime($dn);
        $result[TimeRegisterShiftEnum::NIGHT][TimeRegisterTypeEnum::TRIP] = $this->dntrr->getNightTripsFromDeliveryNoteSortedByTime($dn);
        $result[TimeRegisterShiftEnum::MORNING][TimeRegisterTypeEnum::WORK] = $this->dntrr->getMorningWorksFromDeliveryNoteSortedByTime($dn);
        $result[TimeRegisterShiftEnum::AFTERNOON][TimeRegisterTypeEnum::WORK] = $this->dntrr->getAfternoonWorksFromDeliveryNoteSortedByTime($dn);
        $result[TimeRegisterShiftEnum::NIGHT][TimeRegisterTypeEnum::WORK] = $this->dntrr->getNightWorksFromDeliveryNoteSortedByTime($dn);
        $result[TimeRegisterShiftEnum::MORNING][TimeRegisterTypeEnum::STOP] = $this->dntrr->getMorningStopsFromDeliveryNoteSortedByTime($dn);
        $result[TimeRegisterShiftEnum::AFTERNOON][TimeRegisterTypeEnum::STOP] = $this->dntrr->getAfternoonStopsFromDeliveryNoteSortedByTime($dn);
        $result[TimeRegisterShiftEnum::NIGHT][TimeRegisterTypeEnum::STOP] = $this->dntrr->getNightStopsFromDeliveryNoteSortedByTime($dn);
        $totalHours = 0;
        /** @var DeliveryNoteTimeRegister $dntr */
        foreach ($result[TimeRegisterShiftEnum::MORNING][TimeRegisterTypeEnum::TRIP] as $dntr) {
            $totalHours += $dntr->getDifferenceBetweenEndAndBeginHoursInDecimalHours();
        }
        /** @var DeliveryNoteTimeRegister $dntr */
        foreach ($result[TimeRegisterShiftEnum::AFTERNOON][TimeRegisterTypeEnum::TRIP] as $dntr) {
            $totalHours += $dntr->getDifferenceBetweenEndAndBeginHoursInDecimalHours();
        }
        /** @var DeliveryNoteTimeRegister $dntr */
        foreach ($result[TimeRegisterShiftEnum::NIGHT][TimeRegisterTypeEnum::TRIP] as $dntr) {
            $totalHours += $dntr->getDifferenceBetweenEndAndBeginHoursInDecimalHours();
        }
        $result['total_trip_hours'] = self::getTotalHoursHumanizedString($this->getTotalHoursByType($result, TimeRegisterTypeEnum::TRIP));
        $result['total_work_hours'] = self::getTotalHoursHumanizedString($this->getTotalHoursByType($result, TimeRegisterTypeEnum::WORK));
        $result['total_stop_hours'] = self::getTotalHoursHumanizedString($this->getTotalHoursByType($result, TimeRegisterTypeEnum::STOP));

        return $result;
    }

    /**
     * @param float $hours
     *
     * @return string
     */
    public static function getTotalHoursHumanizedString(float $hours)
    {
        return self::clockalize($hours);
    }

    /**
     * @param float $in
     *
     * @return string
     */
    private static function clockalize(float $in)
    {
        $h = intval($in);
        $m = round((((($in - $h) / 100.0) * 60.0) * 100), 0);
        if ($m == 60) {
            $h++;
            $m = 0;
        }

        return $m == 0 ? sprintf("%dh", $h) : sprintf("%dh %dm", $h, $m);
    }

    /**
     * @param array $result
     * @param int   $type
     *
     * @return float
     */
    private function getTotalHoursByType(array $result, int $type)
    {
        $totalHours = 0.0;
        /** @var DeliveryNoteTimeRegister $dntr */
        foreach ($result[TimeRegisterShiftEnum::MORNING][$type] as $dntr) {
            $totalHours += $dntr->getDifferenceBetweenEndAndBeginHoursInDecimalHours();
        }
        /** @var DeliveryNoteTimeRegister $dntr */
        foreach ($result[TimeRegisterShiftEnum::AFTERNOON][$type] as $dntr) {
            $totalHours += $dntr->getDifferenceBetweenEndAndBeginHoursInDecimalHours();
        }
        /** @var DeliveryNoteTimeRegister $dntr */
        foreach ($result[TimeRegisterShiftEnum::NIGHT][$type] as $dntr) {
            $totalHours += $dntr->getDifferenceBetweenEndAndBeginHoursInDecimalHours();
        }

        return $totalHours;
    }
}
