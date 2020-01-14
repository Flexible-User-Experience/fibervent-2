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
        $result['total_trip_morning_hours'] = self::getTotalHoursHumanizedString($this->getTotalHoursByShiftAndType($result, TimeRegisterShiftEnum::MORNING, TimeRegisterTypeEnum::TRIP));
        $result['total_trip_afternoon_hours'] = self::getTotalHoursHumanizedString($this->getTotalHoursByShiftAndType($result, TimeRegisterShiftEnum::AFTERNOON, TimeRegisterTypeEnum::TRIP));
        $result['total_trip_night_hours'] = self::getTotalHoursHumanizedString($this->getTotalHoursByShiftAndType($result, TimeRegisterShiftEnum::NIGHT, TimeRegisterTypeEnum::TRIP));
        $result['total_work_hours'] = self::getTotalHoursHumanizedString($this->getTotalHoursByType($result, TimeRegisterTypeEnum::WORK));
        $result['total_stop_hours'] = self::getTotalHoursHumanizedString($this->getTotalHoursByType($result, TimeRegisterTypeEnum::STOP));

        return $result;
    }

    /**
     * @param float $hours
     *
     * @return string
     */
    public static function getTotalHoursHumanizedString(?float $hours)
    {
        $result = '---';
        if ($hours) {
            $result = self::clockalize($hours);
        }

        return $result;
    }

    /**
     * @param float $in
     *
     * @return string
     */
    private static function clockalize(float $in)
    {
        $result = '0h';
        $h = intval($in);
        $m = round((((($in - $h) / 100.0) * 60.0) * 100), 0);
        if ($m == 60) {
            $h++;
            $m = 0;
        }
        if ($h == 0 && $m > 0) {
            $result = sprintf("%dm", $m);
        } elseif ($h > 0 && $m == 0) {
            $result = sprintf("%dh", $h);
        } elseif ($h > 0 && $m > 0) {
            $result = sprintf("%dh %dm", $h, $m);
        }

        return $result;
    }

    /**
     * @param array $result
     * @param int   $shift
     * @param int   $type
     *
     * @return float
     */
    private function getTotalHoursByShiftAndType(array $result, int $shift, int $type)
    {
        $totalHours = 0.0;
        /** @var DeliveryNoteTimeRegister $dntr */
        foreach ($result[$shift][$type] as $dntr) {
            $totalHours += $dntr->getDifferenceBetweenEndAndBeginHoursInDecimalHours();
        }

        return $totalHours;
    }

    /**
     * @param array $result
     * @param int   $type
     *
     * @return float
     */
    private function getTotalHoursByType(array $result, int $type)
    {
        $totalHours = $this->getTotalHoursByShiftAndType($result, TimeRegisterShiftEnum::MORNING, $type);
        $totalHours += $this->getTotalHoursByShiftAndType($result, TimeRegisterShiftEnum::AFTERNOON, $type);
        $totalHours += $this->getTotalHoursByShiftAndType($result, TimeRegisterShiftEnum::NIGHT, $type);

        return $totalHours;
    }
}
