<?php

namespace App\Form\Type;

use App\Entity\DamageCategory;
use App\Enum\AuditStatusEnum;
use App\Repository\AuditRepository;
use App\Repository\DamageCategoryRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * WindfarmAnnualStatsFormType class.
 *
 * @category FormType
 */
class WindfarmAnnualStatsFormType extends AbstractType
{
    const BLOCK_PREFIX = 'windfarm_annual_stats';

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var AuditRepository
     */
    private $ar;

    /**
     * @var DamageCategoryRepository
     */
    private $dcr;

    /**
     * Methods.
     */

    /**
     * WindfarmAnnualStatsFormType constructor.
     *
     * @param EntityManagerInterface   $em
     * @param AuditRepository          $ar
     * @param DamageCategoryRepository $dcr
     */
    public function __construct(EntityManagerInterface $em, AuditRepository $ar, DamageCategoryRepository $dcr)
    {
        $this->em = $em;
        $this->ar = $ar;
        $this->dcr = $dcr;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @throws ORMException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $yearsArray = $this->ar->getYearsOfAllAuditsByWindfarm($options['windfarm_id']);

        if (count($yearsArray) > 0) {
            $defaultDamageCategoryData = array();
            $damageCategories = $this->dcr->findAllSortedByCategory();
            /** @var DamageCategory $damageCategory */
            foreach ($damageCategories as $damageCategory) {
                $defaultDamageCategoryData[] = $this->em->getReference('App:DamageCategory', $damageCategory->getId());
            }

            $builder
                ->add(
                    'damage_categories',
                    EntityType::class,
                    array(
                        'mapped' => false,
                        'required' => false,
                        'multiple' => true,
                        'expanded' => true,
                        'label' => 'admin.bladedamage.damagecategory_long',
                        'class' => 'App\Entity\DamageCategory',
                        'query_builder' => $this->dcr->findAllSortedByCategoryQB(),
                        'data' => $defaultDamageCategoryData,
                    )
                )
                ->add(
                    'audit_status',
                    ChoiceType::class,
                    array(
                        'mapped' => false,
                        'required' => false,
                        'multiple' => true,
                        'expanded' => true,
                        'label' => 'admin.audit.status',
                        'choices' => AuditStatusEnum::getReversedEnumArray(),
                        'data' => array(AuditStatusEnum::DONE, AuditStatusEnum::INVOICED),
                    )
                )
                ->add(
                    'year',
                    ChoiceType::class,
                    array(
                        'mapped' => false,
                        'required' => true,
                        'multiple' => false,
                        'label' => 'admin.audit.year',
                        'choices' => $yearsArray,
                    )
                )
                ->add(
                    'generate',
                    SubmitType::class,
                    array(
                        'label' => 'admin.audit.generate',
                        'attr' => array(
                            'class' => 'btn btn-success',
                        ),
                    )
                )
            ;
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'windfarm_id' => null,
            )
        );
    }

    /**
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }
}
