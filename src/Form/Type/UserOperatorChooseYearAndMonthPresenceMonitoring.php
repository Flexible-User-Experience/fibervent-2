<?php

namespace App\Form\Type;

use App\Entity\User;
use App\Enum\MonthsEnum;
use App\Enum\YearsEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
        $builder
            ->add(
                'month',
                ChoiceType::class,
                array(
                    'mapped' => false,
                    'required' => false,
                    'expanded' => false,
                    'multiple' => false,
                    'placeholder' => false,
                    'label' => 'admin.presencemonitoring.form.month',
                    'choices' => MonthsEnum::getMonthEnumArray(),
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

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $now = new \DateTimeImmutable();
            $form = $event->getForm();
            $previousMonthSelected = $form->get('month')->getData();
            $form
                ->add(
                    'month',
                    ChoiceType::class,
                    array(
                        'mapped' => false,
                        'required' => false,
                        'expanded' => false,
                        'multiple' => false,
                        'placeholder' => false,
                        'label' => 'admin.presencemonitoring.form.month',
                        'choices' => MonthsEnum::getMonthEnumArray(),
                        'data' => $previousMonthSelected ?: $now->format('n'),
                    )
                )
            ;
        });
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
