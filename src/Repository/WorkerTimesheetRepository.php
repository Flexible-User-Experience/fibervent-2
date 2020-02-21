<?php

namespace App\Repository;

use App\Entity\DeliveryNote;
use App\Entity\User;
use App\Entity\WorkerTimesheet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class WorkerTimesheetRepository.
 *
 * @category Repository
 */
class WorkerTimesheetRepository extends ServiceEntityRepository
{
    /**
     * WorkerTimesheetRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WorkerTimesheet::class);
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return QueryBuilder
     */
    public function findAllSortedByIdQB($limit = null, $order = 'ASC')
    {
        $query = $this->createQueryBuilder('wt')->orderBy('wt.id', $order);
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
    public function findAllDaysByOperatorYearAndMonthSortedByDateQB(User $operator, $year, $month)
    {
        return $this->createQueryBuilder('wt')
            ->leftJoin('wt.deliveryNote', 'd')
            ->where('wt.worker = :operator')
            ->andWhere('YEAR(d.date) = :year')
            ->andWhere('MONTH(d.date) = :month')
            ->setParameter('operator', $operator)
            ->setParameter('year', $year)
            ->setParameter('month', $month)
            ->orderBy('d.date', 'ASC');
    }

    /**
     * @param User $operator
     * @param int  $year
     * @param int  $month
     *
     * @return Query
     */
    public function findAllDaysByOperatorYearAndMonthSortedByDateQ(User $operator, $year, $month)
    {
        return $this->findAllDaysByOperatorYearAndMonthSortedByDateQB($operator, $year, $month)->getQuery();
    }

    /**
     * @param User $operator
     * @param int  $year
     * @param int  $month
     *
     * @return array
     */
    public function findAllDaysByOperatorYearAndMonthSortedByDate(User $operator, $year, $month)
    {
        return $this->findAllDaysByOperatorYearAndMonthSortedByDateQ($operator, $year, $month)->getResult();
    }

    /**
     * @param DeliveryNote $deliveryNote
     * @param User         $operator
     *
     * @return QueryBuilder
     */
    public function findItemsByDelvireyNoteAndWorkeSortedByDateQB(DeliveryNote $deliveryNote, User $operator)
    {
        return $this->createQueryBuilder('wt')
            ->join('wt.deliveryNote', 'd')
            ->where('wt.worker = :operator')
            ->andWhere('wt.deliveryNote = :delivery')
            ->setParameter('operator', $operator)
            ->setParameter('delivery', $deliveryNote)
            ->orderBy('d.date', 'ASC');
    }

    /**
     * @param DeliveryNote $deliveryNote
     * @param User         $operator
     *
     * @return Query
     */
    public function findItemsByDelvireyNoteAndWorkeSortedByDateQ(DeliveryNote $deliveryNote, User $operator)
    {
        return $this->findItemsByDelvireyNoteAndWorkeSortedByDateQB($deliveryNote, $operator)->getQuery();
    }

    /**
     * @param DeliveryNote $deliveryNote
     * @param User         $operator
     *
     * @return array
     */
    public function findItemsByDelvireyNoteAndWorkeSortedByDate(DeliveryNote $deliveryNote, User $operator)
    {
        return $this->findItemsByDelvireyNoteAndWorkeSortedByDateQ($deliveryNote, $operator)->getResult();
    }
}
