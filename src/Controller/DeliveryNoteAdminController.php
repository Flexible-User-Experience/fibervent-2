<?php

namespace App\Controller;

use App\Entity\DeliveryNote;
use App\Entity\WorkOrder;
use App\Model\AjaxResponse;
use App\Repository\WindfarmRepository;
use App\Repository\WorkOrderRepository;
use App\Service\AuthCustomerService;
use App\Service\DeliveryNotePdfBuilderService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DeliveryNoteAdminController.
 *
 * @category Controller
 */
class DeliveryNoteAdminController extends AbstractBaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     * @throws AccessDeniedHttpException
     */
    public function editAction($id = null)
    {
        if (!$this->getGuardian()->isDeliveryNoteOwnResource($this->getPersistedObject())) {
            throw new AccessDeniedHttpException();
        }

        return parent::editAction($id);
    }

    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     * @throws AccessDeniedHttpException
     */
    public function showAction($id = null)
    {
        if (!$this->getGuardian()->isDeliveryNoteOwnResource($this->getPersistedObject())) {
            throw new AccessDeniedHttpException();
        }

        return parent::showAction($id);
    }

    /**
     * @param int $id WorkOrder ID
     *
     * @return JsonResponse
     */
    public function getWindfarmsFromWorkOrderIdAction($id)
    {
        $ajaxResponse = new AjaxResponse();
        /** @var WorkOrderRepository $wor */
        $wor = $this->container->get('app.work_order_repository');
        /** @var WindfarmRepository $wfr */
        $wfr = $this->container->get('app.windfarm_repository');
        /** @var WorkOrder $workOrder */
        $workOrder = $wor->find($id);
        if (!$workOrder) {
            return new JsonResponse($ajaxResponse);
        }
        $ajaxResponse->setData($wfr->findOnlyRelatedWithAWorkOrderSortedByNameAjax($workOrder));
        $jsonEncodedResult = $ajaxResponse->getJsonEncodedResult();

        return new JsonResponse($jsonEncodedResult);
    }

    /**
     * Export DeliveryNote in PDF format action.
     *
     * @return Response
     *
     * @throws NotFoundHttpException     If the object does not exist
     * @throws AccessDeniedHttpException If access is not granted
     * @throws \Exception
     */
    public function pdfAction()
    {
        /** @var DeliveryNote $object */
        $object = $this->getPersistedObject();
        if (!$this->getGuardian()->isDeliveryNoteOwnResource($object)) {
            throw new AccessDeniedHttpException();
        }

        /** @var DeliveryNotePdfBuilderService $dnpbs */
        $dnpbs = $this->get('app.delivery_note_pdf_builder');
        $pdf = $dnpbs->build($object);

        return new Response($pdf->Output('albaran_'.$object->getId().'.pdf', 'I'), 200, array('Content-type' => 'application/pdf'));
    }

    /**
     * @return DeliveryNote
     * @throws NotFoundHttpException
     */
    private function getPersistedObject()
    {
        $request = $this->resolveRequest();
        $id = $request->get($this->admin->getIdParameter());

        /** @var DeliveryNote $object */
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw $this->createNotFoundException(sprintf('Unable to find delivery note record with id: %s', $id));
        }

        return $object;
    }

    /**
     * @return AuthCustomerService
     */
    private function getGuardian()
    {
        return $this->get('app.auth_customer');
    }
}
