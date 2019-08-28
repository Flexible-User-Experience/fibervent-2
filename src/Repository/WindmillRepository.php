<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Windmill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class WindmillRepository.
 *
 * @category Repository
 *
 * @author   Anton Serra <aserratorta@gmail.com>
 */
class WindmillRepository extends ServiceEntityRepository
{
    /**
     * EventCategoryRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Windmill::class);
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return QueryBuilder
     */
    public function findAllSortedByCustomerWindfarmAndWindmillCodeQB($limit = null, $order = 'ASC')
    {
        $query = $this
            ->createQueryBuilder('windmill')
            ->select('windmill, windfarm, customer')
            ->join('windmill.windfarm', 'windfarm')
            ->join('windfarm.customer', 'customer')
            ->orderBy('customer.name', $order)
            ->addOrderBy('windfarm.name', $order)
            ->addOrderBy('windmill.code', $order);

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
    public function findAllSortedByCustomerWindfarmAndWindmillCodeQ($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByCustomerWindfarmAndWindmillCodeQB($limit, $order)->getQuery();
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return array
     */
    public function findAllSortedByCustomerWindfarmAndWindmillCode($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByCustomerWindfarmAndWindmillCodeQ($limit, $order)->getResult();
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return QueryBuilder
     */
    public function findEnabledSortedByCustomerWindfarmAndWindmillCodeQB($limit = null, $order = 'ASC')
    {
        $query = $this
            ->findAllSortedByCustomerWindfarmAndWindmillCodeQB($limit, $order)
            ->where('windmill.enabled = true');

        return $query;
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return Query
     */
    public function findEnabledSortedByCustomerWindfarmAndWindmillCodeQ($limit = null, $order = 'ASC')
    {
        return $this->findEnabledSortedByCustomerWindfarmAndWindmillCodeQB($limit, $order)->getQuery();
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return array
     */
    public function findEnabledSortedByCustomerWindfarmAndWindmillCode($limit = null, $order = 'ASC')
    {
        return $this->findEnabledSortedByCustomerWindfarmAndWindmillCodeQ($limit, $order)->getResult();
    }

    /**
     * @param Customer $customer
     * @param int|null $limit
     * @param string   $order
     *
     * @return QueryBuilder
     */
    public function findCustomerSortedByCustomerWindfarmAndWindmillCodeQB(Customer $customer, $limit = null, $order = 'ASC')
    {
        return $this->findEnabledSortedByCustomerWindfarmAndWindmillCodeQB($limit, $order)
            ->andWhere('windfarm.customer = :customer')
            ->setParameter('customer', $customer)
        ;
    }

    /**
     * @param Customer $customer
     * @param int|null $limit
     * @param string   $order
     *
     * @return Query
     */
    public function findCustomerSortedByCustomerWindfarmAndWindmillCodeQ(Customer $customer, $limit = null, $order = 'ASC')
    {
        return $this->findCustomerSortedByCustomerWindfarmAndWindmillCodeQB($customer, $limit, $order)->getQuery();
    }

    /**
     * @param Customer $customer
     * @param int|null $limit
     * @param string   $order
     *
     * @return array
     */
    public function findCustomerSortedByCustomerWindfarmAndWindmillCode(Customer $customer, $limit = null, $order = 'ASC')
    {
        return $this->findCustomerSortedByCustomerWindfarmAndWindmillCodeQ($customer, $limit, $order)->getResult();
    }
}
