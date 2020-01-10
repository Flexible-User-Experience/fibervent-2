<?php

namespace App\Repository;

use App\Entity\DeliveryNote;
use App\Entity\DeliveryNoteTimeRegister;
use App\Enum\TimeRegisterShiftEnum;
use App\Enum\TimeRegisterTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;

/**
 * Class DeliveryNoteTimeRegisterRepository.
 *
 * @category Repository
 */
class DeliveryNoteTimeRegisterRepository extends ServiceEntityRepository
{
    /**
     * DeliveryNoteRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DeliveryNoteTimeRegister::class);
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return QueryBuilder
     */
    public function findAllSortedByIdQB($limit = null, $order = 'ASC')
    {
        $query = $this->createQueryBuilder('t')->orderBy('t.id', $order);
        if (!is_null($limit)) {
            $query->setMaxResults($limit);
        }

        return $query;
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return Query
     */
    public function findAllSortedByIdQ($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByIdQB($limit, $order)->getQuery();
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return array
     */
    public function findAllSortedById($limit = null, $order = 'ASC')
    {
        return $this->findAllSortedByIdQ($limit, $order)->getResult();
    }

    /**
     * @param DeliveryNote $dn
     *
     * @return QueryBuilder
     */
    public function getMorningTripsFromDeliveryNoteSortedByTimeQB(DeliveryNote $dn)
    {
        return $this->buildCommonInternalQuery($dn, TimeRegisterShiftEnum::MORNING, TimeRegisterTypeEnum::TRIP);
    }

    /**
     * @param DeliveryNote $dn
     *
     * @return Query
     */
    public function getMorningTripsFromDeliveryNoteSortedByTimeQ(DeliveryNote $dn)
    {
        return $this->getMorningTripsFromDeliveryNoteSortedByTimeQB($dn)->getQuery();
    }

    /**
     * @param DeliveryNote $dn
     *
     * @return DeliveryNoteTimeRegister[]|array
     */
    public function getMorningTripsFromDeliveryNoteSortedByTime(DeliveryNote $dn)
    {
        return $this->getMorningTripsFromDeliveryNoteSortedByTimeQ($dn)->getResult();
    }

    /**
     * @param DeliveryNote $dn
     *
     * @return QueryBuilder
     */
    public function getAfternoonTripsFromDeliveryNoteSortedByTimeQB(DeliveryNote $dn)
    {
        return $this->buildCommonInternalQuery($dn, TimeRegisterShiftEnum::AFTERNOON, TimeRegisterTypeEnum::TRIP);
    }

    /**
     * @param DeliveryNote $dn
     *
     * @return Query
     */
    public function getAfternoonTripsFromDeliveryNoteSortedByTimeQ(DeliveryNote $dn)
    {
        return $this->getAfternoonTripsFromDeliveryNoteSortedByTimeQB($dn)->getQuery();
    }

    /**
     * @param DeliveryNote $dn
     *
     * @return DeliveryNoteTimeRegister[]|array
     */
    public function getAfternoonTripsFromDeliveryNoteSortedByTime(DeliveryNote $dn)
    {
        return $this->getAfternoonTripsFromDeliveryNoteSortedByTimeQ($dn)->getResult();
    }

    /**
     * @param DeliveryNote $dn
     *
     * @return QueryBuilder
     */
    public function getNightTripsFromDeliveryNoteSortedByTimeQB(DeliveryNote $dn)
    {
        return $this->buildCommonInternalQuery($dn, TimeRegisterShiftEnum::NIGHT, TimeRegisterTypeEnum::TRIP);
    }

    /**
     * @param DeliveryNote $dn
     *
     * @return Query
     */
    public function getNightTripsFromDeliveryNoteSortedByTimeQ(DeliveryNote $dn)
    {
        return $this->getNightTripsFromDeliveryNoteSortedByTimeQB($dn)->getQuery();
    }

    /**
     * @param DeliveryNote $dn
     *
     * @return DeliveryNoteTimeRegister[]|array
     */
    public function getNightTripsFromDeliveryNoteSortedByTime(DeliveryNote $dn)
    {
        return $this->getNightTripsFromDeliveryNoteSortedByTimeQ($dn)->getResult();
    }

    /**
     * @param DeliveryNote $dn
     * @param int          $shift
     * @param int          $type
     *
     * @return QueryBuilder
     */
    private function buildCommonInternalQuery(DeliveryNote $dn, int $shift, int $type)
    {
        return $this->createQueryBuilder('dntr')
            ->where('dntr.deliveryNote = :dn')
            ->andWhere('dntr.type = :type')
            ->andWhere('dntr.shift = :shift')
            ->setParameter('dn', $dn)
            ->setParameter('type', $type)
            ->setParameter('shift', $shift)
            ->orderBy('dntr.begin', 'ASC');
    }
}
