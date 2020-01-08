<?php

namespace App\Controller;

use App\Entity\DeliveryNote;
use App\Service\WorkOrderPdfBuilderService;
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
     * Export WorkOrder in PDF format action.
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

        /** @var WorkOrderPdfBuilderService $apbs */
        $apbs = $this->get('app.workorder_pdf_builder');
        $pdf = $apbs->build($object);

        return new Response($pdf->Output('informe_proyecto_'.$object->getId().'.pdf', 'I'), 200, array('Content-type' => 'application/pdf'));
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
}
