<?php

namespace App\Repository;

use App\Entity\AuditWindmillBlade;
use App\Entity\Observation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class ObservationRepository.
 *
 * @category Repository
 *
 * @author   David Romaní <david@flux.cat>
 */
class ObservationRepository extends ServiceEntityRepository
{
    /**
     * EventCategoryRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Observation::class);
    }

    /**
     * @param AuditWindmillBlade $auditWindmillBlade
     *
     * @return QueryBuilder
     */
    public function getItemsOfAuditWindmillBladeQB(AuditWindmillBlade $auditWindmillBlade)
    {
        $query = $this
            ->createQueryBuilder('o')
            ->where('o.auditWindmillBlade = :awb')
            ->setParameter('awb', $auditWindmillBlade);

        return $query;
    }

    /**
     * @param AuditWindmillBlade $auditWindmillBlade
     *
     * @return Query
     */
    public function getItemsOfAuditWindmillBladeQ(AuditWindmillBlade $auditWindmillBlade)
    {
        return $this->getItemsOfAuditWindmillBladeQB($auditWindmillBlade)->getQuery();
    }

    /**
     * @param AuditWindmillBlade $auditWindmillBlade
     *
     * @return array
     */
    public function getItemsOfAuditWindmillBlade(AuditWindmillBlade $auditWindmillBlade)
    {
        return $this->getItemsOfAuditWindmillBladeQ($auditWindmillBlade)->getResult();
    }

    /**
     * @param AuditWindmillBlade $auditWindmillBlade
     *
     * @return QueryBuilder
     */
    public function getItemsOfAuditWindmillBladeSortedByDamageNumberQB(AuditWindmillBlade $auditWindmillBlade)
    {
        $query = $this
            ->getItemsOfAuditWindmillBladeQB($auditWindmillBlade)
            ->orderBy('o.damageNumber', 'ASC');

        return $query;
    }

    /**
     * @param AuditWindmillBlade $auditWindmillBlade
     *
     * @return Query
     */
    public function getItemsOfAuditWindmillBladeSortedByDamageNumberQ(AuditWindmillBlade $auditWindmillBlade)
    {
        return $this->getItemsOfAuditWindmillBladeSortedByDamageNumberQB($auditWindmillBlade)->getQuery();
    }

    /**
     * @param AuditWindmillBlade $auditWindmillBlade
     *
     * @return array
     */
    public function getItemsOfAuditWindmillBladeSortedByDamageNumber(AuditWindmillBlade $auditWindmillBlade)
    {
        return $this->getItemsOfAuditWindmillBladeSortedByDamageNumberQ($auditWindmillBlade)->getResult();
    }
}
