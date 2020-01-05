<?php

namespace App\Form\Type;

use App\Entity\User;
use App\Enum\MonthsEnum;
use App\Enum\YearsEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * UserOperatorChooseYearAndMonthPresenceMonitoring class.
 *
 * @category FormType
 */
class UserOperatorChooseYearAndMonthPresenceMonitoring extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $now = new \DateTimeImmutable();
        $builder
            ->add(
                'month',
                ChoiceType::class,
                array(
                    'mapped' => false,
                    'required' => true,
                    'multiple' => false,
                    'label' => 'admin.presencemonitoring.form.month',
                    'choices' => MonthsEnum::getMonthEnumArray(),
                    'empty_data' => 3,
                )
            )
            ->add(
                'year',
                ChoiceType::class,
                array(
                    'mapped' => false,
                    'required' => true,
                    'multiple' => false,
                    'label' => 'admin.presencemonitoring.form.year',
                    'choices' => YearsEnum::getYearEnumArray(),
                )
            )
            ->add(
                'generate',
                SubmitType::class,
                array(
                    'label' => 'admin.presencemonitoring.form.send',
                    'attr' => array(
                        'class' => 'btn btn-success',
                    ),
                )
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => User::class,
            )
        );
    }

    /**
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'user_operator_choose_presence_monitoring_period';
    }
}
