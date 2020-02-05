<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Windfarm;
use App\Entity\Windmill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class WindmillRepository.
 *
 * @category Repository
 */
class WindmillRepository extends ServiceEntityRepository
{
    /**
     * WindmillRepository constructor.
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
        $query = $this->createQueryBuilder('windmill')
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
        return $this->findAllSortedByCustomerWindfarmAndWindmillCodeQB($limit, $order)->where('windmill.enabled = true');
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
        return $this->findEnabledSortedByCustomerWindfarmAndWindmillCodeQB($limit, $order)->andWhere('windfarm.customer = :customer')->setParameter('customer', $customer);
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

    /**
     * @param Windfarm $windfarm
     * @param null     $limit
     * @param string   $order
     *
     * @return QueryBuilder
     */
    public function findEnabledandWindfarmSortedByCustomerWindfarmAndWindmillCodeQB(Windfarm $windfarm, $limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByCustomerWindfarmAndWindmillCodeQB()
            ->where('windmill.enabled = true')
            ->andWhere('windmill.windfarm = :windfarm')
            ->setParameter('windfarm', $windfarm);
    }

    /**
     * @param Windfarm $windfarm
     * @param null     $limit
     * @param string   $order
     *
     * @return Query
     */
    public function findEnabledandWindfarmSortedByCustomerWindfarmAndWindmillCodeQ(Windfarm $windfarm, $limit = null, $order = 'ASC')
    {
        return $this->findEnabledandWindfarmSortedByCustomerWindfarmAndWindmillCodeQB($windfarm, $limit, $order)->getQuery();
    }

    /**
     * @param Windfarm $windfarm
     * @param null     $limit
     * @param string   $order
     *
     * @return array
     */
    public function findEnabledandWindfarmSortedByCustomerWindfarmAndWindmillCode(Windfarm $windfarm, $limit = null, $order = 'ASC')
    {
        return $this->findEnabledandWindfarmSortedByCustomerWindfarmAndWindmillCodeQ($windfarm, $limit, $order)->getResult();
    }

    /**
     * @param array $windfarms
     *
     * @return QueryBuilder
     */
    public function findMultipleByWindfarmsArrayQB($windfarms)
    {
        $ids = [];
        /** @var Windfarm $windfarm */
        foreach ($windfarms as $windfarm) {
            $ids[] = $windfarm->getId();
        }
        $query = $this->createQueryBuilder('wm')->orderBy('wm.code', 'ASC');
        $query->where($query->expr()->in('wm.windfarm', $ids));

        return $query;
    }

    /**
     * @param array $windfarms
     *
     * @return Query
     */
    public function findMultipleByWindfarmsArrayQ($windfarms)
    {
        return $this->findMultipleByWindfarmsArrayQB($windfarms)->getQuery();
    }

    /**
     * @param array $windfarms
     *
     * @return array
     */
    public function findMultipleByWindfarmsArray($windfarms)
    {
        return $this->findMultipleByWindfarmsArrayQ($windfarms)->getResult();
    }

    /**
     * @param array $ids
     *
     * @return QueryBuilder
     */
    public function getMultipleByWindfarmsIdsArrayAjaxQB(array $ids)
    {
        $query = $this->createQueryBuilder('wm')
            ->select('wm.code AS text, wm.id')
            ->orderBy('wm.code', 'ASC');
        $query->where($query->expr()->in('wm.windfarm', $ids));

        return $query;
    }

    /**
     * @param array $ids
     *
     * @return Query
     */
    public function getMultipleByWindfarmsIdsArrayAjaxQ(array $ids)
    {
        return $this->getMultipleByWindfarmsIdsArrayAjaxQB($ids)->getQuery();
    }

    /**
     * @param array $ids
     *
     * @return array
     */
    public function getMultipleByWindfarmsIdsArrayAjax(array $ids)
    {
        return $this->getMultipleByWindfarmsIdsArrayAjaxQ($ids)->getResult();
    }
}
