<?php

namespace App\Controller;

use App\Entity\AuditWindmillBlade;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class AuditWindmillBladeAdminController.
 *
 * @category Controller
 */
class AuditWindmillBladeAdminController extends AbstractBaseAdminController
{
    /**
     * Redirect the user depend on this choice.
     *
     * @param AuditWindmillBlade $object
     *
     * @return RedirectResponse
     */
    protected function redirectTo($object)
    {
        $request = $this->getRequest();

        return new RedirectResponse($this->getRedirectToUrl($request, $object, 'admin_app_audit_edit', array('id' => $object->getAudit()->getId())));
    }
}
