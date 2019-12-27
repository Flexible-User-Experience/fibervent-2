<?php

namespace App\Manager;

use App\Entity\BladeDamage;
use App\Entity\Observation;
use App\Repository\BladeDamageRepository;

/**
 * Class ObservationManager.
 *
 * @category Manager
 */
class ObservationManager
{
    /**
     * @var BladeDamageRepository
     */
    private $bdr;

    /**
     * Methods.
     */

    /**
     * ObservationManager constructor.
     *
     * @param BladeDamageRepository $bdr
     */
    public function __construct(BladeDamageRepository $bdr)
    {
        $this->bdr = $bdr;
    }

    /**
     * Calculate the PDF blade damage number from an observation with BC old calculation mode.
     *
     * @param Observation $observation
     *
     * @return int
     */
    public function getPdfBladeDamageNumber(Observation $observation)
    {
        $damageNumber = 1;
        $bladeDamages = $this->bdr->getItemsOfAuditWindmillBladeSortedByRadius($observation->getAuditWindmillBlade());
        /** @var BladeDamage $bladeDamage */
        foreach ($bladeDamages as $bladeDamage) {
            if ($bladeDamage->getId() == $observation->getDamageNumber()) {
                break;
            }
            ++$damageNumber;
        }
        // BC mode
        if ($damageNumber == count($bladeDamages) + 1) {
            $damageNumber = $observation->getDamageNumber();
        }

        return $damageNumber;
    }
}
