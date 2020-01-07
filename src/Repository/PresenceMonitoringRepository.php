<?php

namespace App\Repository;

use App\Entity\PresenceMonitoring;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class PresenceMonitoringRepository.
 *
 * @category Repository
 */
class PresenceMonitoringRepository extends ServiceEntityRepository
{
    /**
     * PresenceMonitoringRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PresenceMonitoring::class);
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return QueryBuilder
     */
    public function findAllSortedByIdQB($limit = null, $order = 'ASC')
    {
        $query = $this->createQueryBuilder('pm')->orderBy('pm.id', $order);
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
     * @param User $operator
     * @param int  $year
     * @param int  $month
     *
     * @return QueryBuilder
     */
    public function findByOperatorYearAndMonthSortedByDateQB(User $operator, $year, $month)
    {
        return $this->createQueryBuilder('pm')
            ->where('pm.worker = :operator')
            ->andWhere('YEAR(pm.date) = :year')
            ->andWhere('MONTH(pm.date) = :month')
            ->setParameter('operator', $operator)
            ->setParameter('year', $year)
            ->setParameter('month', $month)
            ->orderBy('pm.date', 'ASC');
    }

    /**
     * @param User $operator
     * @param int  $year
     * @param int  $month
     *
     * @return Query
     */
    public function findByOperatorYearAndMonthSortedByDateQ(User $operator, $year, $month)
    {
        return $this->findByOperatorYearAndMonthSortedByDateQB($operator, $year, $month)->getQuery();
    }

    /**
     * @param User $operator
     * @param int  $year
     * @param int  $month
     *
     * @return array
     */
    public function findByOperatorYearAndMonthSortedByDate(User $operator, $year, $month)
    {
        return $this->findByOperatorYearAndMonthSortedByDateQ($operator, $year, $month)->getResult();
    }
}
