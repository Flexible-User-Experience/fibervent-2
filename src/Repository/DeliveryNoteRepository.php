<?php

namespace App\Repository;

use App\Entity\DeliveryNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * Class DeliveryNoteRepository.
 *
 * @category Repository
 */
class DeliveryNoteRepository extends ServiceEntityRepository
{
    /**
     * DeliveryNoteRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DeliveryNote::class);
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return QueryBuilder
     */
    public function findAllSortedByIdQB($limit = null, $order = 'ASC')
    {
        $query = $this->createQueryBuilder('dn')->orderBy('dn.id', $order);
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
     * @param null   $limit
     * @param string $order
     *
     * @return QueryBuilder
     */
    public function findAllSortedByDateDescQB($limit = null, $order = 'DESC')
    {
        return $this->findAllSortedByIdQB($limit, $order)->addOrderBy('dn.date', $order);
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return Query
     */
    public function findAllSortedByDateDescQ($limit = null, $order = 'DESC')
    {
        return $this->findAllSortedByDateDescQB($limit, $order)->getQuery();
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return array
     */
    public function findAllSortedByDateDesc($limit = null, $order = 'DESC')
    {
        return $this->findAllSortedByDateDescQ($limit, $order)->getResult();
    }
}
