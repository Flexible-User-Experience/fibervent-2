<?php

namespace App\Service;

use App\Entity\Audit;
use App\Entity\Customer;
use App\Entity\DeliveryNote;
use App\Entity\User;
use App\Entity\Windfarm;
use App\Entity\WorkerTimesheet;
use App\Enum\UserRolesEnum;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * Class AuthCustomerService.
 *
 * @category Service
 */
class AuthCustomerService
{
    /**
     * @var AuthorizationChecker
     */
    private AuthorizationChecker $acs;

    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tss;

    /**
     * Methods.
     */

    /**
     * AuthCustomerService constructor.
     *
     * @param AuthorizationChecker  $acs
     * @param TokenStorageInterface $tss
     */
    public function __construct(AuthorizationChecker $acs, TokenStorageInterface $tss)
    {
        $this->acs = $acs;
        $this->tss = $tss;
    }

    /**
     * @param Windfarm $windfarm
     *
     * @return bool
     */
    public function isWindfarmOwnResource(Windfarm $windfarm)
    {
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            return true;
        }

        if ($this->acs->isGranted(UserRolesEnum::ROLE_CUSTOMER) && $windfarm->getCustomer()->getId() == $this->getUser()->getCustomer()->getId()) {
            return true;
        }

        return false;
    }

    /**
     * @param Audit $audit
     *
     * @return bool
     */
    public function isAuditOwnResource(Audit $audit)
    {
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            return true;
        }

        if ($this->acs->isGranted(UserRolesEnum::ROLE_CUSTOMER) && $audit->getCustomer()->getId() == $this->getUser()->getCustomer()->getId()) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isCustomerUser()
    {
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            return false;
        }

        if ($this->acs->isGranted(UserRolesEnum::ROLE_CUSTOMER)) {
            return true;
        }

        return false;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->getUser()->getCustomer();
    }

    /**
     * @param DeliveryNote $deliveryNote
     *
     * @return bool
     */
    public function isDeliveryNoteOwnResource(DeliveryNote $deliveryNote)
    {
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            return true;
        }

        if ($this->acs->isGranted(UserRolesEnum::ROLE_TECHNICIAN) && (
            ($deliveryNote->getTeamLeader() && $deliveryNote->getTeamLeader()->getId() == $this->getUser()->getId()) ||
            ($deliveryNote->getTeamTechnician1() && $deliveryNote->getTeamTechnician1()->getId() == $this->getUser()->getId()) ||
            ($deliveryNote->getTeamTechnician2() && $deliveryNote->getTeamTechnician2()->getId() == $this->getUser()->getId()) ||
            ($deliveryNote->getTeamTechnician3() && $deliveryNote->getTeamTechnician3()->getId() == $this->getUser()->getId()) ||
            ($deliveryNote->getTeamTechnician4() && $deliveryNote->getTeamTechnician4()->getId() == $this->getUser()->getId()))
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param WorkerTimesheet $workerTimesheet
     *
     * @return bool
     */
    public function isWorkerTimesheetOwnResource(WorkerTimesheet $workerTimesheet)
    {
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            return true;
        }

        if (($this->acs->isGranted(UserRolesEnum::ROLE_OPERATOR) || $this->acs->isGranted(UserRolesEnum::ROLE_TECHNICIAN)) && $workerTimesheet->getWorker()->getId() == $this->getUser()->getId()) {
            return true;
        }

        return false;
    }

    /**
     * @return User|object|string
     */
    private function getUser()
    {
        return $this->tss->getToken()->getUser();
    }
}
