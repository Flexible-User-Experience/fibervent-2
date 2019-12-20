<?php

namespace App\Form\Type;

use App\Enum\WindfarmLanguageEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * UserProfileFormType class.
 *
 * @category FormType
 */
class UserProfileFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                TextType::class,
                array(
                    'label' => 'admin.user.username',
                    'required' => false,
                    'disabled' => true,
                )
            )
            ->add(
                'email',
                EmailType::class,
                array(
                    'label' => 'admin.user.email',
                    'required' => false,
                    'disabled' => true,
                )
            )
            ->add(
                'firstname',
                TextType::class,
                array(
                    'label' => 'admin.user.firstname',
                    'required' => true,
                )
            )
            ->add(
                'lastname',
                TextType::class,
                array(
                    'label' => 'admin.user.lastname',
                    'required' => true,
                )
            )
            ->add(
                'phone',
                TextType::class,
                array(
                    'label' => 'admin.user.phone',
                    'required' => false,
                )
            )
            ->add(
                'imageFile',
                FileType::class,
                array(
                    'label' => ' ',
                    'required' => false,
                )
            )
            ->add(
                'language',
                ChoiceType::class,
                array(
                    'label' => 'admin.user.language',
                    'choices' => WindfarmLanguageEnum::getEnumArrayString(),
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                )
            )
            ->add(
                'submit',
                SubmitType::class,
                array(
                    'label' => 'admin.user.update',
                    'attr' => array(
                        'class' => 'btn btn-success',
                    ),
                )
            );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'user_profile';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'App\Entity\User',
            )
        );
    }
}
