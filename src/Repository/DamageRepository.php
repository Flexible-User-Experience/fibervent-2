<?php

namespace App\Repository;

use App\Entity\Damage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Gedmo\Translatable\TranslatableListener;

/**
 * Class DamageRepository.
 *
 * @category Repository
 *
 * @author   Anton Serra <aserratorta@gmail.com>
 */
class DamageRepository extends ServiceEntityRepository
{
    /**
     * EventCategoryRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Damage::class);
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return QueryBuilder
     */
    public function findAllEnabledSortedByCodeQB($limit = null, $order = 'ASC')
    {
        $query = $this
            ->createQueryBuilder('d')
            ->where('d.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('d.code', $order);

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
    public function findAllEnabledSortedByCodeQ($limit = null, $order = 'ASC')
    {
        return $this->findAllEnabledSortedByCodeQB($limit, $order)->getQuery();
    }

    /**
     * @param int|null $limit
     * @param string   $order
     *
     * @return array
     */
    public function findAllEnabledSortedByCode($limit = null, $order = 'ASC')
    {
        return $this->findAllEnabledSortedByCodeQ($limit, $order)->getResult();
    }

    /**
     * @param int $id
     *
     * @return QueryBuilder
     */
    public function localizedFindQB($id)
    {
        $query = $this
            ->createQueryBuilder('d')
            ->where('d.id = :id')
            ->setParameter('id', $id);

        return $query;
    }

    /**
     * @param int    $id
     * @param string $locale
     *
     * @return Query
     */
    public function localizedFindQ($id, $locale)
    {
        $query = $this->localizedFindQB($id)->getQuery();
        $query
            ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker')
            ->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale)
            ->setHint(TranslatableListener::HINT_FALLBACK, 1);

        return $query;
    }

    /**
     * @param int    $id
     * @param string $locale
     *
     * @return Damage
     */
    public function localizedFind($id, $locale)
    {
        return $this->localizedFindQ($id, $locale)->getOneOrNullResult();
    }

    /**
     * @param int    $id
     * @param string $locale
     *
     * @return string
     */
    public function getlocalizedDesciption($id, $locale)
    {
        $queryBuilder = $this
            ->createQueryBuilder('d')
            ->select('d.description')
            ->where('d.id = :id')
            ->setParameter('id', $id);

        $query = $queryBuilder
            ->getQuery()
            ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker')
            ->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);

        return $query->getOneOrNullResult()['description'];
    }
}
