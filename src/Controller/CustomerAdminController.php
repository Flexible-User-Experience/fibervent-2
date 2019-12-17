<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Manager\CustomerAjaxResponseManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CustomerAdminController.
 *
 * @category Controller
 */
class CustomerAdminController extends AbstractBaseAdminController
{
    /**
     * Create windfarms map view.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException     If the object does not exist
     * @throws AccessDeniedHttpException If access is not granted
     */
    public function mapAction(Request $request = null)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var Customer $object */
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw $this->createNotFoundException(sprintf('Unable to find customer record with id: %s', $id));
        }

        // Customer filter
        if ($this->get('app.auth_customer')->isCustomerUser()) {
            throw new AccessDeniedHttpException();
        }

        return $this->renderWithExtraParams(
            ':Admin/Customer:map.html.twig',
            array(
                'action' => 'show',
                'object' => $object,
            )
        );
    }

    /**
     * @param int $id Customer ID
     *
     * @return JsonResponse
     */
    public function getAuditsFromCustomerIdAction($id)
    {
        /** @var CustomerAjaxResponseManager $carm */
        $carm = $this->get('app.manager_customer_ajax_response');

        /** @var Customer $object */
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw $this->createNotFoundException(sprintf('Unable to find customer record with id: %s', $id));
        }

        // Customer filter
        if ($this->get('app.auth_customer')->isCustomerUser()) {
            throw new AccessDeniedHttpException();
        }

        return new JsonResponse($carm->getAuditsAjaxResponseFromCustomerId($id));
    }
}
