<?php

namespace App\Repository;

use App\Entity\WindmillBlade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry as RegistryInterface;

/**
 * Class WindmillBladeRepository.
 *
 * @category Repository
 *
 * @author   Anton Serra <aserratorta@gmail.com>
 */
class WindmillBladeRepository extends ServiceEntityRepository
{
    /**
     * EventCategoryRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WindmillBlade::class);
    }
}
