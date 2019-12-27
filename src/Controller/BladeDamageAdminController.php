<?php

namespace App\Controller;

use App\Entity\BladeDamage;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class BladeDamageAdminController.
 *
 * @category Controller
 */
class BladeDamageAdminController extends AbstractBaseAdminController
{
    /**
     * Redirect the user depend on this choice.
     *
     * @param BladeDamage $object
     *
     * @return RedirectResponse
     */
    protected function redirectTo($object)
    {
        $request = $this->getRequest();

        return new RedirectResponse($this->getRedirectToUrl($request, $object, 'admin_app_auditwindmillblade_edit', array('id' => $object->getAuditWindmillBlade()->getId())));
    }
}
