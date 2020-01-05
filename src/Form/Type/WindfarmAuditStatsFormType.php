<?php

namespace App\Form\Type;

use App\Enum\AuditStatusEnum;
use App\Repository\AuditRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * WindfarmAuditStatsFormType class.
 *
 * @category FormType
 */
class WindfarmAuditStatsFormType extends AbstractType
{
    const BLOCK_PREFIX = 'windfarm_audit_stats';

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var AuditRepository
     */
    private $ar;

    /**
     * Methods.
     */

    /**
     * WindfarmAnnualStatsFormType constructor.
     *
     * @param EntityManagerInterface $em
     * @param AuditRepository        $ar
     */
    public function __construct(EntityManagerInterface $em, AuditRepository $ar)
    {
        $this->em = $em;
        $this->ar = $ar;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $yearsArray = $this->ar->getYearsOfAllAuditsByWindfarm($options['windfarm_id']);

        if (count($yearsArray) > 0) {
            $builder
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
                    'dates_range',
                    DateRangePickerType::class,
                    array(
                        'mapped' => false,
                        'required' => false,
                        'field_options' => array(
                            'format' => 'dd-MM-yyyy',
                        ),
                        'label' => 'admin.audit.dates_range',
                    )
                )
                ->add(
                    'generate',
                    SubmitType::class,
                    array(
                        'label' => 'admin.windfarm.select',
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
