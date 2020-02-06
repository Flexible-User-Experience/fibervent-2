<?php

namespace App\Manager;

use App\Entity\Audit;
use App\Entity\AuditWindmillBlade;
use App\Entity\BladeDamage;
use App\Entity\WorkOrder;
use App\Entity\WorkOrderTask;
use App\Enum\WorkOrderStatusEnum;
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
     * @param Audit[]|array $audits
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
     * @param Audit[]|array $audits
     *
     * @return WorkOrder
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createWorkOrderFromAudits($audits)
    {
        $workOrder = new WorkOrder();
        $workOrder
            ->setCustomer($audits[0]->getCustomer())
            ->setIsFromAudit(true)
            ->setWindfarm($audits[0]->getWindfarm())
            ->setStatus(WorkOrderStatusEnum::PENDING)
        ;
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
                            $workOrderTask = $this->createWorkOrderTask($workOrder, $bladeDamage);
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

    /**
     * @param Audit[]|array $audits
     * @param int           $damageCategoryLevel minimum level necessary to create the corresponding task
     *
     * @return WorkOrder
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createWorkOrderFromAuditsForDamageCategoryLevel($audits, $damageCategoryLevel)
    {
        $workOrder = new WorkOrder();
        $workOrder
            ->setCustomer($audits[0]->getCustomer())
            ->setIsFromAudit(true)
            ->setWindfarm($audits[0]->getWindfarm())
            ->setStatus(WorkOrderStatusEnum::PENDING)
        ;
        $this->em->persist($workOrder);
        /** @var Audit $audit */
        foreach ($audits as $audit) {
            $auditWindmillBlades = $audit->getAuditWindmillBlades();
            $workOrder
                ->addAudit($audit)
                ->addWindfarm($audit->getWindfarm())
            ;
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
                            if ($bladeDamage->getDamageCategory()->getCategory() >= $damageCategoryLevel) {
                                $workOrderTask = $this->createWorkOrderTask($workOrder, $bladeDamage);
                                $this->em->persist($workOrderTask);
                            }
                        }
                    }
                }
            }
        }
        $this->em->persist($workOrder);
        $this->em->flush();

        return $workOrder;
    }

    /**
     * @param WorkOrder   $workOrder
     * @param BladeDamage $bladeDamage
     *
     * @return WorkOrderTask
     */
    private function createWorkOrderTask(WorkOrder $workOrder, BladeDamage $bladeDamage)
    {
        $workOrderTask = new WorkOrderTask();
        $workOrderTask
            ->setWorkOrder($workOrder)
            ->setIsFromAudit(true)
            ->setBladeDamage($bladeDamage)
            ->setDescription($bladeDamage->getDamage()->getDescription())
            ->setWindmillBlade($bladeDamage->getAuditWindmillBlade()->getWindmillBlade())
            ->setWindmill($bladeDamage->getAuditWindmillBlade()->getWindmillBlade()->getWindmill())
            ->setPosition($bladeDamage->getPosition())
            ->setRadius($bladeDamage->getRadius())
            ->setDistance($bladeDamage->getDistance())
            ->setSize($bladeDamage->getSize())
            ->setEdge($bladeDamage->getEdge())
            ->setIsCompleted(false)
        ;

        return $workOrderTask;
    }
}
