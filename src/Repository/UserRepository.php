<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\DeliveryNote;
use App\Enum\UserRolesEnum;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class UserRepository.
 *
 * @category Repository
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return QueryBuilder
     */
    public function findAllSortedByNameQB($limit = null, $order = 'ASC')
    {
        $query = $this->createQueryBuilder('u')->orderBy('u.lastname', $order)->addOrderBy('u.firstname', $order);
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
    public function getEnabledWorkersSortedByNameQB($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByNameQB($limit, $order)
            ->where('u.enabled = :enabled')
            ->andWhere('u.roles LIKE :operator OR u.roles LIKE :technician')
            ->setParameter('operator', '%'.UserRolesEnum::ROLE_OPERATOR.'%')
            ->setParameter('technician', '%'.UserRolesEnum::ROLE_TECHNICIAN.'%')
            ->setParameter('enabled', true);
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return Query
     */
    public function getEnabledWorkersSortedByNameQ($limit = null, $order = 'ASC')
    {
        return $this->getEnabledWorkersSortedByNameQB($limit, $order)->getQuery();
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return array
     */
    public function getEnabledWorkersSortedByName($limit = null, $order = 'ASC')
    {
        return $this->getEnabledWorkersSortedByNameQ($limit, $order)->getResult();
    }

    /**
     * @param Customer|null $customer
     * @param int|null      $limit
     * @param string        $order
     *
     * @return QueryBuilder
     */
    public function findOnlyAvailableSortedByNameQB($customer, $limit = null, $order = 'ASC')
    {
        $query = $this->findAllSortedByNameQB($limit, $order);
        $query->where('u.customer IS NULL')
            ->orWhere('u.customer = :customer')
            ->setParameter('customer', $customer);

        return $query;
    }

    /**
     * @param Customer|null $customer
     * @param int|null      $limit
     * @param string        $order
     *
     * @return Query
     */
    public function findOnlyAvailableSortedByNameQ($customer, $limit = null, $order = 'ASC')
    {
        return $this->findOnlyAvailableSortedByNameQB($customer, $limit, $order)->getQuery();
    }

    /**
     * @param Customer|null $customer
     * @param int|null      $limit
     * @param string        $order
     *
     * @return array
     */
    public function findOnlyAvailableSortedByName($customer, $limit = null, $order = 'ASC')
    {
        return $this->findOnlyAvailableSortedByNameQ($customer, $limit, $order)->getResult();
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return QueryBuilder
     */
    public function findAllTechniciansSortedByNameQB($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByNameQB($limit, $order)
            ->where('u.roles NOT LIKE :role')
            ->andWhere('u.enabled = true')
            ->setParameter('role', '%'.UserRolesEnum::ROLE_CUSTOMER.'%');
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return Query
     */
    public function findAllTechniciansSortedByNameQ($limit = null, $order = 'ASC')
    {
        return $this->findAllTechniciansSortedByNameQB($limit, $order)->getQuery();
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return array
     */
    public function findAllTechniciansSortedByName($limit = null, $order = 'ASC')
    {
        return $this->findAllTechniciansSortedByNameQ($limit, $order)->getResult();
    }

    /**
     * @param Customer|null $customer
     * @param int|null      $limit
     * @param string        $order
     *
     * @return QueryBuilder
     */
    public function findEnabledSortedByNameQB($customer, $limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByNameQB($limit, $order)
            ->where('u.enabled = true AND u.customer IS NULL')
            ->orWhere('u.enabled = true AND u.customer = :customer')
            ->setParameter('customer', $customer);
    }

    /**
     * @param Customer|null $customer
     * @param int|null      $limit
     * @param string        $order
     *
     * @return Query
     */
    public function findEnabledSortedByNameQ($customer, $limit = null, $order = 'ASC')
    {
        return $this->findEnabledSortedByNameQB($customer, $limit, $order)->getQuery();
    }

    /**
     * @param Customer|null $customer
     * @param int|null      $limit
     * @param string        $order
     *
     * @return array
     */
    public function findEnabledSortedByName($customer, $limit = null, $order = 'ASC')
    {
        return $this->findEnabledSortedByNameQ($customer, $limit, $order)->getResult();
    }

    /**
     * @param Customer $customer
     * @param int|null $limit
     * @param string   $order
     *
     * @return QueryBuilder
     */
    public function findRegionalManagersByCustomerQB(Customer $customer, $limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByNameQB($limit, $order)
            ->where('u.customer = :customer')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('customer', $customer)
            ->setParameter('role', '%'.UserRolesEnum::ROLE_CUSTOMER.'%');
    }

    /**
     * @param Customer $customer
     * @param int|null $limit
     * @param string   $order
     *
     * @return Query
     */
    public function findRegionalManagersByCustomerQ(Customer $customer, $limit = null, $order = 'ASC')
    {
        return $this->findRegionalManagersByCustomerQB($customer, $limit, $order)->getQuery();
    }

    /**
     * @param Customer $customer
     * @param int|null $limit
     * @param string   $order
     *
     * @return array
     */
    public function findRegionalManagersByCustomer(Customer $customer, $limit = null, $order = 'ASC')
    {
        return $this->findRegionalManagersByCustomerQ($customer, $limit, $order)->getResult();
    }

    /**
     * @param DeliveryNote $deliveryNote
     *
     * @return QueryBuilder
     */
    public function getWorkersRelatedWithADeliveryNoteSortedByNameAjaxQB(DeliveryNote $deliveryNote)
    {
        $query = $this->createQueryBuilder('u')
            ->select("CONCAT_WS(' ', u.firstname, u.lastname) AS text, u.id")
            ->orderBy('u.lastname', 'ASC')
            ->addOrderBy('u.firstname', 'ASC');
        $workersIdsArray = [];
        if ($deliveryNote->getTeamLeader()) {
            $workersIdsArray[] = $deliveryNote->getTeamLeader()->getId();
        }
        if ($deliveryNote->getTeamTechnician1()) {
            $workersIdsArray[] = $deliveryNote->getTeamTechnician1()->getId();
        }
        if ($deliveryNote->getTeamTechnician2()) {
            $workersIdsArray[] = $deliveryNote->getTeamTechnician2()->getId();
        }
        if ($deliveryNote->getTeamTechnician3()) {
            $workersIdsArray[] = $deliveryNote->getTeamTechnician3()->getId();
        }
        if ($deliveryNote->getTeamTechnician4()) {
            $workersIdsArray[] = $deliveryNote->getTeamTechnician4()->getId();
        }
        $query->where($query->expr()->in('u.id', $workersIdsArray));

        return $query;
    }

    /**
     * @param DeliveryNote $deliveryNote
     *
     * @return Query
     */
    public function getWorkersRelatedWithADeliveryNoteSortedByNameAjaxQ(DeliveryNote $deliveryNote)
    {
        return $this->getWorkersRelatedWithADeliveryNoteSortedByNameAjaxQB($deliveryNote)->getQuery();
    }

    /**
     * @param DeliveryNote $deliveryNote
     *
     * @return array
     */
    public function getWorkersRelatedWithADeliveryNoteSortedByNameAjax(DeliveryNote $deliveryNote)
    {
        return $this->getWorkersRelatedWithADeliveryNoteSortedByNameAjaxQ($deliveryNote)->getResult();
    }
}
