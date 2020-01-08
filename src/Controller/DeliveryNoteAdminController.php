<?php

namespace App\Controller;

use App\Entity\DeliveryNote;
use App\Service\DeliveryNotePdfBuilderService;
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
}
