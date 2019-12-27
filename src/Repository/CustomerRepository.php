<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * Class CustomerRepository.
 *
 * @category Repository
 */
class CustomerRepository extends ServiceEntityRepository
{
    /**
     * CustomerRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return QueryBuilder
     */
    public function findAllSortedByNameQB($limit = null, $order = 'ASC')
    {
        $query = $this->createQueryBuilder('c')->orderBy('c.name', $order);
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
    public function findAllSortedByNameQ($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByNameQB($limit, $order)->getQuery();
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return array
     */
    public function findAllSortedByName($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByNameQ($limit, $order)->getResult();
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return QueryBuilder
     */
    public function findEnabledSortedByNameQB($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByNameQB($limit, $order)->where('c.enabled = true');
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return Query
     */
    public function findEnabledSortedByNameQ($limit = null, $order = 'ASC')
    {
        return $this->findEnabledSortedByNameQB($limit, $order)->getQuery();
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return array
     */
    public function findEnabledSortedByName($limit = null, $order = 'ASC')
    {
        return $this->findEnabledSortedByNameQ($limit, $order)->getResult();
    }
}
