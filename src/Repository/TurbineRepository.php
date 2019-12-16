<?php

namespace App\Repository;

use App\Entity\Turbine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry as RegistryInterface;

/**
 * Class TurbineRepository.
 *
 * @category Repository
 *
 * @author   Anton Serra <aserratorta@gmail.com>
 */
class TurbineRepository extends ServiceEntityRepository
{
    /**
     * EventCategoryRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Turbine::class);
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return QueryBuilder
     */
    public function findAllSortedByModelQB($limit = null, $order = 'ASC')
    {
        $query = $this
            ->createQueryBuilder('t')
            ->orderBy('t.model', $order);

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
    public function findAllSortedByModelQ($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByModelQB($limit, $order)->getQuery();
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return array
     */
    public function findAllSortedByName($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByModelQ($limit, $order)->getResult();
    }
}
