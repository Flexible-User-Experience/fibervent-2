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
 */
class BladeDamageRepository extends ServiceEntityRepository
{
    /**
     * BladeDamageRepository constructor.
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
        return $this->createQueryBuilder('bd')
            ->where('bd.auditWindmillBlade = :awb')
            ->setParameter('awb', $auditWindmillBlade)
            ->orderBy('bd.radius', 'ASC');
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
