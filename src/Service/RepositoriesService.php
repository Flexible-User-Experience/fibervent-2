<?php

namespace App\Service;

use App\Repository\AuditRepository;
use App\Repository\BladeDamageRepository;
use App\Repository\BladeRepository;
use App\Repository\CustomerRepository;
use App\Repository\DamageCategoryRepository;
use App\Repository\DamageRepository;
use App\Repository\StateRepository;
use App\Repository\TurbineRepository;
use App\Repository\UserRepository;
use App\Repository\WindfarmRepository;
use App\Repository\WindmillBladeRepository;
use App\Repository\WindmillRepository;

/**
 * Class RepositoriesService.
 *
 * @category Service
 */
class RepositoriesService
{
    /**
     * @var CustomerRepository
     */
    private $cr;

    /**
     * @var UserRepository
     */
    private $ur;

    /**
     * @var WindmillRepository
     */
    private $wmr;

    /**
     * @var WindfarmRepository
     */
    private $wfr;

    /**
     * @var BladeRepository
     */
    private $br;

    /**
     * @var TurbineRepository
     */
    private $tr;

    /**
     * @var StateRepository
     */
    private $sr;

    /**
     * @var DamageRepository
     */
    private $dr;

    /**
     * @var DamageCategoryRepository
     */
    private $dcr;

    /**
     * @var AuditRepository
     */
    private $ar;

    /**
     * @var WindmillBladeRepository
     */
    private $wbr;

    /**
     * @var BladeDamageRepository
     */
    private $bdr;

    /**
     * Methods.
     */

    /**
     * RepositoriesService constructor.
     *
     * @param CustomerRepository       $cr
     * @param UserRepository           $ur
     * @param WindmillRepository       $wmr
     * @param WindfarmRepository       $wfr
     * @param BladeRepository          $br
     * @param TurbineRepository        $tr
     * @param StateRepository          $sr
     * @param DamageRepository         $dr
     * @param DamageCategoryRepository $dcr
     * @param AuditRepository          $ar
     * @param WindmillBladeRepository  $wbr
     * @param BladeDamageRepository    $bdr
     */
    public function __construct(CustomerRepository $cr, UserRepository $ur, WindmillRepository $wmr, WindfarmRepository $wfr, BladeRepository $br, TurbineRepository $tr, StateRepository $sr, DamageRepository $dr, DamageCategoryRepository $dcr, AuditRepository $ar, WindmillBladeRepository $wbr, BladeDamageRepository $bdr)
    {
        $this->cr = $cr;
        $this->ur = $ur;
        $this->wmr = $wmr;
        $this->wfr = $wfr;
        $this->br = $br;
        $this->tr = $tr;
        $this->sr = $sr;
        $this->dr = $dr;
        $this->dcr = $dcr;
        $this->ar = $ar;
        $this->wbr = $wbr;
        $this->bdr = $bdr;
    }

    /**
     * @return CustomerRepository
     */
    public function getCr()
    {
        return $this->cr;
    }

    /**
     * @return UserRepository
     */
    public function getUr()
    {
        return $this->ur;
    }

    /**
     * @return WindmillRepository
     */
    public function getWmr()
    {
        return $this->wmr;
    }

    /**
     * @return WindfarmRepository
     */
    public function getWfr()
    {
        return $this->wfr;
    }

    /**
     * @return BladeRepository
     */
    public function getBr()
    {
        return $this->br;
    }

    /**
     * @return TurbineRepository
     */
    public function getTr()
    {
        return $this->tr;
    }

    /**
     * @return StateRepository
     */
    public function getSr()
    {
        return $this->sr;
    }

    /**
     * @return DamageRepository
     */
    public function getDr()
    {
        return $this->dr;
    }

    /**
     * @return DamageCategoryRepository
     */
    public function getDcr()
    {
        return $this->dcr;
    }

    /**
     * @return AuditRepository
     */
    public function getAr()
    {
        return $this->ar;
    }

    /**
     * @return WindmillBladeRepository
     */
    public function getWbr()
    {
        return $this->wbr;
    }

    /**
     * @return BladeDamageRepository
     */
    public function getBdr()
    {
        return $this->bdr;
    }
}
