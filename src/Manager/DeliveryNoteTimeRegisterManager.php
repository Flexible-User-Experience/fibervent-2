<?php

namespace App\Manager;

use App\Entity\DeliveryNote;
use App\Entity\DeliveryNoteTimeRegister;
use App\Enum\TimeRegisterShiftEnum;
use App\Enum\TimeRegisterTypeEnum;
use App\Repository\DeliveryNoteTimeRegisterRepository;

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

        return $result;
    }
}
