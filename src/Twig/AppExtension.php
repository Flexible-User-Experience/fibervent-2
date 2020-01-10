<?php

namespace App\Twig;

use App\Entity\AuditWindmillBlade;
use App\Entity\Damage;
use App\Entity\DamageCategory;
use App\Entity\PresenceMonitoring;
use App\Enum\AuditTypeEnum;
use App\Enum\PresenceMonitoringCategoryEnum;
use App\Factory\WindmillBladesDamagesHelperFactory;
use App\Manager\DeliveryNoteTimeRegisterManager;
use App\Repository\DamageRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class AppExtension.
 *
 * @category Twig
 */
class AppExtension extends AbstractExtension
{
    /**
     * @var DamageRepository
     */
    private DamageRepository $dr;

    /**
     * @var WindmillBladesDamagesHelperFactory
     */
    private WindmillBladesDamagesHelperFactory $wbdhf;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $ts;

    /**
     * Methods.
     */

    /**
     * AppExtension constructor.
     *
     * @param DamageRepository                   $dr
     * @param WindmillBladesDamagesHelperFactory $wbdhf
     * @param TranslatorInterface                $ts
     */
    public function __construct(DamageRepository $dr, WindmillBladesDamagesHelperFactory $wbdhf, TranslatorInterface $ts)
    {
        $this->dr = $dr;
        $this->wbdhf = $wbdhf;
        $this->ts = $ts;
    }

    /**
     * Filters.
     */

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('humanized_audit_type', array($this, 'filterHumanizedAuditType')),
            new TwigFilter('get_humanized_total_hours', array($this, 'getHumanizedTotalHours')),
            new TwigFilter('draw_presence_monitory_category_label', array($this, 'drawPresenceMonitoryCategoryLabel')),
        );
    }

    /**
     * @param int $type
     *
     * @return string
     */
    public function filterHumanizedAuditType($type)
    {
        return AuditTypeEnum::getEnumArray()[$type];
    }

    /**
     * @param int|float|null $hours
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getHumanizedTotalHours($hours)
    {
        return DeliveryNoteTimeRegisterManager::getTotalHoursHumanizedString($hours);
    }

    /**
     * @param PresenceMonitoring $pm
     *
     * @return string
     */
    public function drawPresenceMonitoryCategoryLabel(PresenceMonitoring $pm) {
        $cssClass = 'default';
        if ($pm->getCategory() == PresenceMonitoringCategoryEnum::WORKDAY) {
            $cssClass = 'success';
        } elseif ($pm->getCategory() == PresenceMonitoringCategoryEnum::DAYOFF) {
            $cssClass = 'warning';
        } elseif ($pm->getCategory() == PresenceMonitoringCategoryEnum::PERMITS) {
            $cssClass = 'primary';
        } elseif ($pm->getCategory() == PresenceMonitoringCategoryEnum::LEAVE) {
            $cssClass = 'danger';
        }

        return '<span class="label label-'.$cssClass.'">'.$this->ts->trans($pm->getCategoryString()).'</span>';
    }

    /**
     * Functions.
     */

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('get_localized_description', array($this, 'getlocalizedDescription')),
            new TwigFunction('is_row_available', array($this, 'isRowAvailable')),
            new TwigFunction('mark_damage_category', array($this, 'markDamageCategory')),
        );
    }

    /**
     * @param Damage $object
     * @param string $locale
     *
     * @return string
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getlocalizedDescription(Damage $object, $locale)
    {
        return $this->dr->getlocalizedDesciption($object->getId(), $locale);
    }

    /**
     * Decide if a row is hidden or not by DamageCategory and an array of codes.
     *
     * @param DamageCategory $object
     * @param array          $availableCodes
     *
     * @return bool
     */
    public function isRowAvailable(DamageCategory $object, $availableCodes)
    {
        $result = false;
        if (in_array((string) $object->getCategory(), $availableCodes)) {
            $result = true;
        }

        return $result;
    }

    /**
     * @param DamageCategory     $damageCategory
     * @param AuditWindmillBlade $auditWindmillBlade
     *
     * @return string
     */
    public function markDamageCategory(DamageCategory $damageCategory, AuditWindmillBlade $auditWindmillBlade)
    {
        return $this->wbdhf->markDamageCategory($damageCategory, $auditWindmillBlade);
    }
}
