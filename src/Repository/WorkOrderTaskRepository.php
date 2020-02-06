<?php

namespace App\Repository;

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
     * @param null   $limit
     * @param string $order
     *
     * @return QueryBuilder
     */
    public function findAllSortedByIdQB($limit = null, $order = 'ASC')
    {
        $query = $this->createQueryBuilder('t')->orderBy('t.id', $order);
        if (!is_null($limit)) {
            $query->setMaxResults($limit);
        }

        return $query;
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return Query
     */
    public function findAllSortedByIdQ($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByIdQB($limit, $order)->getQuery();
    }

    /**
     * @param null   $limit
     * @param string $order
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
            ->where('t.workOrder = :workOrder')
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
}
