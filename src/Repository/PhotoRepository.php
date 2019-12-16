<?php

namespace App\Repository;

use App\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry as RegistryInterface;

/**
 * Class PhotoRepository.
 *
 * @category Repository
 *
 * @author   Anton Serra <aserratorta@gmail.com>
 */
class PhotoRepository extends ServiceEntityRepository
{
    /**
     * EventCategoryRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Photo::class);
    }
}
