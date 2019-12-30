<?php

namespace App\Service;

use Symfony\Component\Form\FormInterface;

/**
 * Class NotificationService.
 *
 * @category Service
 */
class NotificationService
{
    /**
     * @var CourierService
     */
    private $messenger;

    /**
     * Methods.
     */

    /**
     * NotificationService constructor.
     *
     * @param CourierService $messenger
     */
    public function __construct(CourierService $messenger)
    {
        $this->messenger = $messenger;
    }

    /**
     * Deliver PDF Audit.
     *
     * @param FormInterface $form
     * @param string        $attatchmentPath
     */
    public function deliverAuditEmail(FormInterface $form, $attatchmentPath)
    {
        $to = $form->get('to')->getData();
        $cc = $form->get('cc')->getData();
        $subject = $form->get('subject')->getData();
        $message = $form->get('message')->getData();

        $this->messenger->sendEmailWithCCAndAttatchment(
            'info@fibervent.com',
            $to,
            $subject,
            $message,
            $cc,
            $attatchmentPath
        );
    }
}
