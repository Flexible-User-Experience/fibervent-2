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
        $morningTrips = $this->dntrr->getMorningTripsFromDeliveryNoteSortedByTime($dn);
        $result = array();
        $result[TimeRegisterShiftEnum::MORNING][TimeRegisterTypeEnum::TRIP] = $morningTrips;

        return $result;
    }
}
