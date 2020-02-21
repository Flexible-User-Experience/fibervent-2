<?php

namespace App\Repository;

use App\Entity\Windmill;
use App\Entity\WorkOrder;
use App\Entity\WorkOrderTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class WorkOrderTaskRepository.
 *
 * @category Repository
 */
class WorkOrderTaskRepository extends ServiceEntityRepository
{
    /**
     * WorkOrderTaskRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WorkOrderTask::class);
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return QueryBuilder
     */
    public function findAllSortedByIdQB($limit = null, $order = 'ASC')
    {
        $query = $this->createQueryBuilder('wot')->orderBy('wot.id', $order);
        if (!is_null($limit)) {
            $query->setMaxResults($limit);
        }

        return $query;
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return Query
     */
    public function findAllSortedByIdQ($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByIdQB($limit, $order)->getQuery();
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return array
     */
    public function findAllSortedById($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByIdQ($limit, $order)->getResult();
    }

    /**
     * @param WorkOrder $workOrder
     *
     * @return QueryBuilder
     */
    public function findItemsByWorkOrderSortedByIdQB(WorkOrder $workOrder)
    {
        return $this->findAllSortedByIdQB()
            ->where('wot.workOrder = :workOrder')
            ->setParameter('workOrder', $workOrder)
        ;
    }

    /**
     * @param WorkOrder $workOrder
     *
     * @return Query
     */
    public function findItemsByWorkOrderSortedByIdQ(WorkOrder $workOrder)
    {
        return $this->findItemsByWorkOrderSortedByIdQB($workOrder)->getQuery();
    }

    /**
     * @param WorkOrder $workOrder
     *
     * @return array
     */
    public function findItemsByWorkOrderSortedById(WorkOrder $workOrder)
    {
        return $this->findItemsByWorkOrderSortedByIdQ($workOrder)->getResult();
    }

    /**
     * @param WorkOrder $workOrder
     * @param Windmill  $windmill
     *
     * @return QueryBuilder
     */
    public function findItemsByWorkOrderAndWindmillSortedByIdQB(WorkOrder $workOrder, Windmill $windmill)
    {
        return $this->findItemsByWorkOrderSortedByIdQB($workOrder)
            ->andWhere('wot.windmill = :windmill')
            ->setParameter('windmill', $windmill)
        ;
    }

    /**
     * @param WorkOrder $workOrder
     * @param Windmill  $windmill
     *
     * @return Query
     */
    public function findItemsByWorkOrderAndWindmillSortedByIdQ(WorkOrder $workOrder, Windmill $windmill)
    {
        return $this->findItemsByWorkOrderAndWindmillSortedByIdQB($workOrder, $windmill)->getQuery();
    }

    /**
     * @param WorkOrder $workOrder
     * @param Windmill  $windmill
     *
     * @return array
     */
    public function findItemsByWorkOrderAndWindmillSortedById(WorkOrder $workOrder, Windmill $windmill)
    {
        return $this->findItemsByWorkOrderAndWindmillSortedByIdQ($workOrder, $windmill)->getResult();
    }
}
