<?php

namespace App\Admin;

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
use App\Service\RepositoriesService;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class BaseAdmin.
 *
 * @category Admin
 */
abstract class AbstractBaseAdmin extends AbstractAdmin
{
    /**
     * @var AuthorizationChecker
     */
    protected $acs;

    /**
     * @var TokenStorage
     */
    protected $tss;

    /**
     * @var CustomerRepository
     */
    protected $cr;

    /**
     * @var UserRepository
     */
    protected $ur;

    /**
     * @var WindmillRepository
     */
    protected $wmr;

    /**
     * @var WindfarmRepository
     */
    protected $wfr;

    /**
     * @var BladeRepository
     */
    protected $br;

    /**
     * @var TurbineRepository
     */
    protected $tr;

    /**
     * @var StateRepository
     */
    protected $sr;

    /**
     * @var DamageRepository
     */
    protected $dr;

    /**
     * @var DamageCategoryRepository
     */
    protected $dcr;

    /**
     * @var AuditRepository
     */
    protected $ar;

    /**
     * @var WindmillBladeRepository
     */
    protected $wbr;

    /**
     * @var BladeDamageRepository
     */
    protected $bdr;

    /**
     * @var UploaderHelper
     */
    protected $vus;

    /**
     * @var CacheManager
     */
    protected $lis;

    /**
     * Methods.
     */

    /**
     * @param string               $code
     * @param string               $class
     * @param string               $baseControllerName
     * @param AuthorizationChecker $acs
     * @param TokenStorage         $tss
     * @param RepositoriesService  $rs
     * @param UploaderHelper       $vus
     * @param CacheManager         $lis
     */
    public function __construct($code, $class, $baseControllerName, AuthorizationChecker $acs, TokenStorage $tss, RepositoriesService $rs, UploaderHelper $vus, CacheManager $lis)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->acs = $acs;
        $this->tss = $tss;
        $this->cr = $rs->getCr();
        $this->ur = $rs->getUr();
        $this->wmr = $rs->getWmr();
        $this->wfr = $rs->getWfr();
        $this->br = $rs->getBr();
        $this->tr = $rs->getTr();
        $this->sr = $rs->getSr();
        $this->dr = $rs->getDr();
        $this->dcr = $rs->getDcr();
        $this->ar = $rs->getAr();
        $this->wbr = $rs->getWbr();
        $this->bdr = $rs->getBdr();
        $this->vus = $vus;
        $this->lis = $lis;
    }

    /**
     * @var array
     */
    protected $perPageOptions = array(25, 50, 100, 200);

    /**
     * @var int
     */
    protected $maxPerPage = 25;

    /**
     * Configure route collection.
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('show')
            ->remove('batch');
    }

    /**
     * Remove batch action list view first column.
     *
     * @return array
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        unset($actions['delete']);

        return $actions;
    }

    /**
     * Get export formats.
     *
     * @return array
     */
    public function getExportFormats()
    {
        return array(
            'csv',
            'xls',
        );
    }

    /**
     * @param string $bootstrapGrid
     * @param string $bootstrapSize
     * @param string $boxClass
     *
     * @return array
     */
    protected function getDefaultFormBoxArray($bootstrapGrid = 'md', $bootstrapSize = '6', $boxClass = 'primary')
    {
        return array(
            'class' => 'col-'.$bootstrapGrid.'-'.$bootstrapSize,
            'box_class' => 'box box-'.$boxClass,
        );
    }

    /**
     * @param string $bootstrapColSize
     *
     * @return array
     */
    protected function getFormMdSuccessBoxArray($bootstrapColSize = '6')
    {
        return $this->getDefaultFormBoxArray('md', $bootstrapColSize, 'success');
    }

    /**
     * Get image helper form mapper with thumbnail.
     *
     * @param int $minWidth
     *
     * @return string
     */
    protected function getImageHelperFormMapperWithThumbnail($minWidth = 1200)
    {
        return ($this->getSubject() ? $this->getSubject()->getImageName() ? '<img src="'.$this->lis->getBrowserPath(
                $this->vus->asset($this->getSubject(), 'imageFile'),
                '480xY'
            ).'" class="admin-preview img-responsive" alt="thumbnail"/>' : '' : '').'<span style="width:100%;display:block;">'.$this->trans('admin.photo.help', ['%width%' => $minWidth]).'</span>';
    }
}
