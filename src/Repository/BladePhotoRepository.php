<?php

namespace App\Repository;

use App\Entity\BladePhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * Class BladePhotoRepository.
 *
 * @category Repository
 */
class BladePhotoRepository extends ServiceEntityRepository
{
    /**
     * BladePhotoRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BladePhoto::class);
    }
}
