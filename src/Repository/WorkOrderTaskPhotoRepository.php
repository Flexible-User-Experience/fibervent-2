<?php

namespace App\Repository;

use App\Entity\WorkOrderTaskPhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * Class WorkOrderTaskPhotoRepository.
 *
 * @category Repository
 */
class WorkOrderTaskPhotoRepository extends ServiceEntityRepository
{
    /**
     * BladePhotoRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WorkOrderTaskPhoto::class);
    }
}
