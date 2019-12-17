<?php

namespace App\Form\Type;

use App\Enum\AuditDiagramTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * AuditDiagramTypeFormType class.
 *
 * @category FormType
 */
class AuditDiagramTypeFormType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'choices' => AuditDiagramTypeEnum::getEnumArray(),
            )
        );
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'audit_diagram_type';
    }
}
