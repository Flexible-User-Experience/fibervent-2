<?php

namespace App\Repository;

use App\Entity\Windfarm;
use App\Entity\Windmill;
use App\Entity\WindmillBlade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class WindmillBladeRepository.
 *
 * @category Repository
 */
class WindmillBladeRepository extends ServiceEntityRepository
{
    /**
     * WindmillBladeRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WindmillBlade::class);
    }

    /**
     * @param Windmill $windmill
     * @param null     $limit
     * @param string   $order
     *
     * @return QueryBuilder
     */
    public function findWindmillSortedByCodeQB(Windmill $windmill, $limit = null, $order = 'ASC')
    {
        $query = $this->createQueryBuilder('wb')
            ->where('wb.windmill = :windmill')
            ->setParameter('windmill', $windmill)
            ->orderBy('wb.order', $order);
        if (!is_null($limit)) {
            $query->setMaxResults($limit);
        }

        return $query;
    }

    /**
     * @param Windmill $windmill
     * @param null     $limit
     * @param string   $order
     *
     * @return Query
     */
    public function findWindmillSortedByCodeQ(Windmill $windmill, $limit = null, $order = 'ASC')
    {
        return $this->findWindmillSortedByCodeQB($windmill, $limit, $order)->getQuery();
    }

    /**
     * @param Windmill $windmill
     * @param null     $limit
     * @param string   $order
     *
     * @return array
     */
    public function findWindmillSortedByCode(Windmill $windmill, $limit = null, $order = 'ASC')
    {
        return $this->findWindmillSortedByCodeQ($windmill, $limit, $order)->getResult();
    }

    /**
     * @param Windmill $windmill
     * @param null     $limit
     * @param string   $order
     *
     * @return QueryBuilder
     */
    public function findWindmillSortedByCodeAjaxQB(Windmill $windmill, $limit = null, $order = 'ASC')
    {
        $query = $this->createQueryBuilder('wb')
            ->select('wb.id, wb.code, wb.order AS text')
            ->where('wb.windmill = :windmill')
            ->setParameter('windmill', $windmill)
            ->orderBy('wb.order', $order);
        if (!is_null($limit)) {
            $query->setMaxResults($limit);
        }

        return $query;
    }

    /**
     * @param Windmill $windmill
     * @param null     $limit
     * @param string   $order
     *
     * @return Query
     */
    public function findWindmillSortedByCodeAjaxQ(Windmill $windmill, $limit = null, $order = 'ASC')
    {
        return $this->findWindmillSortedByCodeAjaxQB($windmill, $limit, $order)->getQuery();
    }

    /**
     * @param Windmill $windmill
     * @param null     $limit
     * @param string   $order
     *
     * @return array
     */
    public function findWindmillSortedByCodeAjax(Windmill $windmill, $limit = null, $order = 'ASC')
    {
        return $this->findWindmillSortedByCodeAjaxQ($windmill, $limit, $order)->getResult();
    }

    /**
     * @return QueryBuilder
     */
    public function findEmptyResultQB()
    {
        return $this->createQueryBuilder('wb')->where('wb.id < 0');
    }

    /**
     * @return Query
     */
    public function findEmptyResultQ()
    {
        return $this->findEmptyResultQB()->getQuery();
    }

    /**
     * @return array
     */
    public function findEmptyResult()
    {
        return $this->findEmptyResultQ()->getResult();
    }

    /**
     * @return QueryBuilder
     */
    public function getAllSortedByWindmillAndOrderQB()
    {
        return $this->createQueryBuilder('wb')
            ->where('wb.id > 0')
            ->orderBy('wb.order', 'ASC')
        ;
    }

    /**
     * @return Query
     */
    public function getAllSortedByWindmillAndOrderQ()
    {
        return $this->getAllSortedByWindmillAndOrderQB()->getQuery();
    }

    /**
     * @return array
     */
    public function getAllSortedByWindmillAndOrder()
    {
        return $this->getAllSortedByWindmillAndOrderQ()->getResult();
    }

    /**
     * @param array $windfarms
     *
     * @return QueryBuilder
     */
    public function findMultipleByWindfarmsArrayQB($windfarms)
    {
        $ids = [0];
        /** @var Windfarm $windfarm */
        foreach ($windfarms as $windfarm) {
            $ids[] = $windfarm->getId();
        }
        $query = $this->createQueryBuilder('wb')
            ->join('wb.windmill', 'wm')
            ->orderBy('wb.order', 'ASC')
        ;
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
}
