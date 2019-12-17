<?php

namespace App\Repository;

use App\Entity\Blade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * Class BladeRepository.
 *
 * @category Repository
 */
class BladeRepository extends ServiceEntityRepository
{
    /**
     * BladeRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Blade::class);
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return QueryBuilder
     */
    public function findAllSortedByModelQB($limit = null, $order = 'ASC')
    {
        $query = $this->createQueryBuilder('b')->orderBy('b.model', $order);
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
    public function findAllSortedByModelQ($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByModelQB($limit, $order)->getQuery();
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return array
     */
    public function findAllSortedByModel($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByModelQ($limit, $order)->getResult();
    }
}
