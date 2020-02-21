<?php

namespace App\Form\Type;

use App\Entity\DeliveryNote;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * DeliveryNoteEmailSendFormType class.
 *
 * @category FormType
 */
class DeliveryNoteEmailSendFormType extends AuditEmailSendFormType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => DeliveryNote::class,
                'default_subject' => null,
                'default_msg' => null,
                'to_emails_list' => null,
                'cc_emails_list' => null,
            )
        );
    }

    /**
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'delivery_note_email_send';
    }
}
