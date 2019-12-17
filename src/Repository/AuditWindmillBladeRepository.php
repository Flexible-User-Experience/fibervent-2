<?php

namespace App\Repository;

use App\Entity\AuditWindmillBlade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * Class AuditWindmillBladeRepository.
 *
 * @category Repository
 */
class AuditWindmillBladeRepository extends ServiceEntityRepository
{
    /**
     * AuditWindmillBladeRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AuditWindmillBlade::class);
    }
}
