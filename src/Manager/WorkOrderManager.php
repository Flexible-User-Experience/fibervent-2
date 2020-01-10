<?php

namespace App\Manager;

use App\Entity\Audit;
use App\Entity\AuditWindmillBlade;
use App\Entity\BladeDamage;
use App\Entity\WorkOrder;
use App\Entity\WorkOrderTask;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;

/**
 * Class WorkOrderManager
 *
 * @category Manager
 */
class WorkOrderManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * Methods.
     */

    /**
     * WorkOrderManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param $audits Audit[]
     *
     * @return bool
     */
    public function checkIfAllAuditsBelongToOneWindfarm($audits)
    {
        $check = true;
        $windfarm = $audits[0]->getWindfarm();
        foreach ($audits as $audit) {
            if ($audit->getWindfarm()->getId() !== $windfarm->getId()) {
                $check = false;
            }
        }

        return $check;
    }

    /**
     * @param $audits Audit[]
     *
     * @return WorkOrder
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createWorkOrderFromAudits($audits)
    {
        $workOrder = new WorkOrder();
        $workOrder->setCustomer($audits[0]->getCustomer());
        $workOrder->setIsFromAudit(true);
        $workOrder->setWindfarm($audits[0]->getWindfarm());
        $this->em->persist($workOrder);
        /** @var Audit $audit */
        foreach ($audits as $audit) {
            $auditWindmillBlades = $audit->getAuditWindmillBlades();
            $workOrder->addAudit($audit);
            if (!$audit->isHasWorkOrder()) {
                $audit->setHasWorkOrder(true);
                $this->em->persist($audit);
            }
            if ($auditWindmillBlades->count() > 0) {
                /** @var AuditWindmillBlade $auditWindmillBlade */
                foreach ($auditWindmillBlades as $auditWindmillBlade) {
                    $bladeDamages = $auditWindmillBlade->getBladeDamages();
                    if ($bladeDamages->count() > 0) {
                        /** @var BladeDamage $bladeDamage */
                        foreach ($bladeDamages as $bladeDamage) {
                            $workOrderTask = new WorkOrderTask();
                            $workOrderTask->setWorkOrder($workOrder);
                            $workOrderTask->setIsFromAudit(true);
                            $workOrderTask->setBladeDamage($bladeDamage);
                            $workOrderTask->setDescription($bladeDamage->getDamage()->getDescription());
                            $workOrderTask->setWindmillBlade($bladeDamage->getAuditWindmillBlade()->getWindmillBlade());
                            $workOrderTask->setWindmill($bladeDamage->getAuditWindmillBlade()->getWindmillBlade()->getWindmill());
                            $workOrderTask->setPosition($bladeDamage->getPosition());
                            $workOrderTask->setRadius($bladeDamage->getRadius());
                            $workOrderTask->setDistance($bladeDamage->getDistance());
                            $workOrderTask->setSize($bladeDamage->getSize());
                            $workOrderTask->setEdge($bladeDamage->getEdge());
                            $workOrderTask->setIsCompleted(false);
                            $this->em->persist($workOrderTask);
                        }
                    }
                }
            }
        }
        $this->em->persist($workOrder);
        $this->em->flush();

        return $workOrder;
    }
}
