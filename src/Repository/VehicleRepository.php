<?php

namespace App\Repository;

use App\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class VehicleRepository.
 *
 * @category Repository
 */
class VehicleRepository extends ServiceEntityRepository
{
    /**
     * VehicleRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Vehicle::class);
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
     * @return QueryBuilder
     */
    public function findAllSortedByNameQB()
    {
        return $this->createQueryBuilder('t')->orderBy('t.name', 'ASC');
    }

    /**
     * @return Query
     */
    public function findAllSortedByNameQ()
    {
        return $this->findAllSortedByNameQB()->getQuery();
    }

    /**
     * @return array|Vehicle[]
     */
    public function findAllSortedByName()
    {
        return $this->findAllSortedByNameQ()->getResult();
    }

    /**
     * @return QueryBuilder
     */
    public function findEnabledSortedByNameQB()
    {
        return $this->findAllSortedByNameQB()
            ->where('t.active = :enabled')
            ->setParameter('enabled', true);
    }

    /**
     * @return Query
     */
    public function findEnabledSortedByNameQ()
    {
        return $this->findEnabledSortedByNameQB()->getQuery();
    }

    /**
     * @return array|Vehicle[]
     */
    public function findEnabledSortedByName()
    {
        return $this->findEnabledSortedByNameQ()->getResult();
    }
}
