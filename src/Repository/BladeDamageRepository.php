<?php

namespace App\Repository;

use App\Entity\AuditWindmillBlade;
use App\Entity\BladeDamage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * Class BladeDamageRepository.
 *
 * @category Repository
 *
 * @author   Anton Serra <aserratorta@gmail.com>
 */
class BladeDamageRepository extends ServiceEntityRepository
{
    /**
     * EventCategoryRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BladeDamage::class);
    }

    /**
     * @param AuditWindmillBlade $auditWindmillBlade
     *
     * @return QueryBuilder
     */
    public function getItemsOfAuditWindmillBladeSortedByRadiusQB(AuditWindmillBlade $auditWindmillBlade)
    {
        $query = $this
            ->createQueryBuilder('bd')
            ->where('bd.auditWindmillBlade = :awb')
            ->setParameter('awb', $auditWindmillBlade)
            ->orderBy('bd.radius', 'ASC');

        return $query;
    }

    /**
     * @param AuditWindmillBlade $auditWindmillBlade
     *
     * @return Query
     */
    public function getItemsOfAuditWindmillBladeSortedByRadiusQ(AuditWindmillBlade $auditWindmillBlade)
    {
        return $this->getItemsOfAuditWindmillBladeSortedByRadiusQB($auditWindmillBlade)->getQuery();
    }

    /**
     * @param AuditWindmillBlade $auditWindmillBlade
     *
     * @return array
     */
    public function getItemsOfAuditWindmillBladeSortedByRadius(AuditWindmillBlade $auditWindmillBlade)
    {
        return $this->getItemsOfAuditWindmillBladeSortedByRadiusQ($auditWindmillBlade)->getResult();
    }
}
