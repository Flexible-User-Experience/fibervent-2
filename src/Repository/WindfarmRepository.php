<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Windfarm;
use App\Entity\WorkOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class WindfarmRepository.
 *
 * @category Repository
 */
class WindfarmRepository extends ServiceEntityRepository
{
    /**
     * WindfarmRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Windfarm::class);
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return QueryBuilder
     */
    public function findAllSortedByNameQB($limit = null, $order = 'ASC')
    {
        $query = $this->createQueryBuilder('w')->orderBy('w.name', $order);
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
    public function findAllSortedByNameQ($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByNameQB($limit, $order)->getQuery();
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return array
     */
    public function findAllSortedByName($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByNameQ($limit, $order)->getResult();
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return QueryBuilder
     */
    public function findEnabledSortedByNameQB($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByNameQB($limit, $order)->where('w.enabled = true');
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return Query
     */
    public function findEnabledSortedByNameQ($limit = null, $order = 'ASC')
    {
        return $this->findEnabledSortedByNameQB($limit, $order)->getQuery();
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return array
     */
    public function findEnabledSortedByName($limit = null, $order = 'ASC')
    {
        return $this->findEnabledSortedByNameQ($limit, $order)->getResult();
    }

    /**
     * @param Customer $customer
     * @param int|null $limit
     * @param string   $order
     *
     * @return QueryBuilder
     */
    public function findOnlyAvailableSortedByNameQB(Customer $customer, $limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByNameQB($limit, $order)
            ->where('w.customer IS NULL')
            ->orWhere('w.customer = :customer')
            ->setParameter('customer', $customer);
    }

    /**
     * @param Customer $customer
     * @param int|null $limit
     * @param string   $order
     *
     * @return Query
     */
    public function findOnlyAvailableSortedByNameQ(Customer $customer, $limit = null, $order = 'ASC')
    {
        return $this->findOnlyAvailableSortedByNameQB($customer, $limit, $order)->getQuery();
    }

    /**
     * @param Customer $customer
     * @param int|null $limit
     * @param string   $order
     *
     * @return array
     */
    public function findOnlyAvailableSortedByName(Customer $customer, $limit = null, $order = 'ASC')
    {
        return $this->findOnlyAvailableSortedByNameQ($customer, $limit, $order)->getResult();
    }

    /**
     * @param Customer $customer
     * @param int|null $limit
     * @param string   $order
     *
     * @return QueryBuilder
     */
    public function findCustomerEnabledSortedByNameQB(Customer $customer, $limit = null, $order = 'ASC')
    {
        return $this->findEnabledSortedByNameQB($limit, $order)->andWhere('w.customer = :customer')->setParameter('customer', $customer);
    }

    /**
     * @param Customer $customer
     * @param int|null $limit
     * @param string   $order
     *
     * @return Query
     */
    public function findCustomerEnabledSortedByNameQ(Customer $customer, $limit = null, $order = 'ASC')
    {
        return $this->findCustomerEnabledSortedByNameQB($customer, $limit, $order)->getQuery();
    }

    /**
     * @param Customer $customer
     * @param int|null $limit
     * @param string   $order
     *
     * @return array
     */
    public function findCustomerEnabledSortedByName(Customer $customer, $limit = null, $order = 'ASC')
    {
        return $this->findCustomerEnabledSortedByNameQ($customer, $limit, $order)->getResult();
    }

    /**
     * @param Customer $customer
     * @param int|null $limit
     * @param string   $order
     *
     * @return QueryBuilder
     */
    public function findCustomerEnabledSortedByNameAjaxQB(Customer $customer, $limit = null, $order = 'ASC')
    {
        return $this->findEnabledSortedByNameQB($limit, $order)
            ->select('w.name, w.id')
            ->andWhere('w.customer = :customer')
            ->setParameter('customer', $customer);
    }

    /**
     * @param Customer $customer
     * @param int|null $limit
     * @param string   $order
     *
     * @return Query
     */
    public function findCustomerEnabledSortedByNameAjaxQ(Customer $customer, $limit = null, $order = 'ASC')
    {
        return $this->findCustomerEnabledSortedByNameAjaxQB($customer, $limit, $order)->getQuery();
    }

    /**
     * @param Customer $customer
     * @param int|null $limit
     * @param string   $order
     *
     * @return array
     */
    public function findCustomerEnabledSortedByNameAjax(Customer $customer, $limit = null, $order = 'ASC')
    {
        return $this->findCustomerEnabledSortedByNameAjaxQ($customer, $limit, $order)->getResult();
    }

    /**
     * @param WorkOrder $workOrder
     *
     * @return QueryBuilder
     */
    public function findOnlyRelatedWithAWorkOrderSortedByNameQB(WorkOrder $workOrder)
    {
        $query = $this
            ->createQueryBuilder('w')
            ->andWhere('w.customer = :customer')
            ->setParameter('customer', $workOrder->getCustomer())
            ->orderBy('w.name', 'ASC')
        ;
        if (count($workOrder->getWindfarms()) > 0) {
            $wfia = array();
            /** @var Windfarm $windfarm */
            foreach ($workOrder->getWindfarms() as $windfarm) {
                $wfia[] = $windfarm->getId();
            }
            $query->andWhere($query->expr()->in('w.id',$wfia));
        }

        return $query;
    }

    /**
     * @param WorkOrder $workOrder
     *
     * @return Query
     */
    public function findOnlyRelatedWithAWorkOrderSortedByNameQ(WorkOrder $workOrder)
    {
        return $this->findOnlyRelatedWithAWorkOrderSortedByNameQB($workOrder)->getQuery();
    }

    /**
     * @param WorkOrder $workOrder
     *
     * @return array
     */
    public function findOnlyRelatedWithAWorkOrderSortedByName(WorkOrder $workOrder)
    {
        return $this->findOnlyRelatedWithAWorkOrderSortedByNameQ($workOrder)->getResult();
    }
}
