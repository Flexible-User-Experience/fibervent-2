<?php

namespace App\Repository;

use App\Entity\WorkOrder;
use App\Enum\WorkOrderStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class WorkOrderRepository.
 *
 * @category Repository
 */
class WorkOrderRepository extends ServiceEntityRepository
{
    /**
     * WorkOrderRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WorkOrder::class);
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return QueryBuilder
     */
    public function findAllSortedByProjectNumberQB($limit = null, $order = 'ASC')
    {
        $query = $this->createQueryBuilder('t')->orderBy('t.projectNumber', $order);
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
    public function findAllSortedByProjectNumberQ($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByProjectNumberQB($limit, $order)->getQuery();
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return array
     */
    public function findAllSortedByProjectNumber($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByProjectNumberQ($limit, $order)->getQuery();
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return QueryBuilder
     */
    public function findAvailableSortedByProjectNumberQB($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByProjectNumberQB($limit, $order)
            ->where('t.status = :pending')
            ->orWhere('t.status = :doing')
            ->setParameter('pending', WorkOrderStatusEnum::PENDING)
            ->setParameter('doing', WorkOrderStatusEnum::DOING)
        ;
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return Query
     */
    public function findAvailableSortedByProjectNumberQ($limit = null, $order = 'ASC')
    {
        return $this->findAvailableSortedByProjectNumberQB($limit, $order)->getQuery();
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return array
     */
    public function findAvailableSortedByProjectNumber($limit = null, $order = 'ASC')
    {
        return $this->findAvailableSortedByProjectNumberQ($limit, $order)->getResult();
    }
}
