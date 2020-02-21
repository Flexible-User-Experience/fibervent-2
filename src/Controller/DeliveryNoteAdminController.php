<?php

namespace App\Controller;

use App\Entity\DeliveryNote;
use App\Entity\User;
use App\Entity\WorkOrder;
use App\Form\Type\DeliveryNoteEmailSendFormType;
use App\Model\AjaxResponse;
use App\Repository\WindfarmRepository;
use App\Repository\WorkOrderRepository;
use App\Service\DeliveryNotePdfBuilderService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @throws Exception
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

        return new Response($pdf->Output('Albaran_'.$object->getId().'.pdf', 'I'), 200, array('Content-type' => 'application/pdf'));
    }

    /**
     * Send and email to cusotmer with an attached  DeliveryNote in PDF format action.
     *
     * @param Request|null $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException     If the object does not exist
     * @throws AccessDeniedHttpException If access is not granted
     * @throws Exception
     */
    public function emailAction(Request $request = null)
    {
        /** @var DeliveryNote $object */
        $object = $this->getPersistedObject();
        if (!$this->getGuardian()->isDeliveryNoteOwnResource($object)) {
            throw new AccessDeniedHttpException();
        }

        /** @var DeliveryNotePdfBuilderService $dnpbs */
        $dnpbs = $this->get('app.delivery_note_pdf_builder');
        $pdf = $dnpbs->build($object);
        $pdf->Output($this->getDestDeliveryNoteFilePath($object), 'F');

        $form = $this->createForm(DeliveryNoteEmailSendFormType::class, $object, array(
            'default_subject' => 'Albarán relativo a la orden de trabajo Fibervent',
            'default_msg' => 'Adjunto albarán número '.$object->getId().' relativo a la orden de trabajo '.$object->getWorkOrder()->getProjectNumber(),
            'to_emails_list' => $this->getReversedToEmailsList($object),
            'cc_emails_list' => $this->getReversedCcEmailsList($object),
        ));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $to = $form->get('to')->getData();
            $cc = $form->get('cc')->getData();
            $this->get('app.notification')->deliverAuditEmail($form, $this->getDestDeliveryNoteFilePath($object)); // TODO
            $this->addFlash('sonata_flash_success', 'El alabrán núm. '.$object->getId().' se ha enviado correctamente a '.$to.($cc ? ' con copia para '.$cc : ''));

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->renderWithExtraParams(
            'Admin/DeliveryNote/email.html.twig',
            array(
                'action' => 'show',
                'object' => $object,
                'form' => $form->createView(),
                'pdf_short_path' => $this->getShortDeliveryNoteFilePath($object),
            )
        );
    }

    /**
     * @param DeliveryNote $deliveryNote
     *
     * @return string
     */
    private function getDestDeliveryNoteFilePath(DeliveryNote $deliveryNote)
    {
        $krd = $this->getParameter('kernel.project_dir');

        return $krd.DIRECTORY_SEPARATOR.'public'.$this->getShortDeliveryNoteFilePath($deliveryNote);
    }

    /**
     * @param DeliveryNote $deliveryNote
     *
     * @return string
     */
    private function getShortDeliveryNoteFilePath(DeliveryNote $deliveryNote)
    {
        return DIRECTORY_SEPARATOR.'pdfs'.DIRECTORY_SEPARATOR.'Albaran-'.$deliveryNote->getId().'.pdf';
    }
    /**
     * @param DeliveryNote $deliveryNote
     *
     * @return array
     */
    private function getToEmailsList(DeliveryNote $deliveryNote)
    {
        return $this->commonEmailsList($deliveryNote);
    }

    /**
     * @param DeliveryNote $deliveryNote
     *
     * @return array
     */
    private function getReversedToEmailsList(DeliveryNote $deliveryNote)
    {
        return array_flip($this->commonEmailsList($deliveryNote));
    }

    /**
     * @param DeliveryNote $deliveryNote
     *
     * @return array
     */
    private function getCcEmailsList(DeliveryNote $deliveryNote)
    {
        $availableMails = $this->commonEmailsList($deliveryNote);
        $users = $this->get('app.user_repository')->findOnlyAvailableSortedByName($deliveryNote->getWorkOrder()->getCustomer());
        /** @var User $user */
        foreach ($users as $user) {
            $availableMails[$user->getEmail()] = $user->getFullname().' <'.$user->getEmail().'>';
        }

        return $availableMails;
    }

    /**
     * @param DeliveryNote $deliveryNote
     *
     * @return array
     */
    private function getReversedCcEmailsList(DeliveryNote $deliveryNote)
    {
        return array_flip($this->getCcEmailsList($deliveryNote));
    }

    /**
     * @param DeliveryNote $deliveryNote
     *
     * @return array
     */
    private function commonEmailsList(DeliveryNote $deliveryNote)
    {
        $availableMails = array();
        if ($deliveryNote->getWorkOrder()->getCustomer()->getEmail()) {
            $availableMails[$deliveryNote->getWorkOrder()->getCustomer()->getEmail()] = $deliveryNote->getWorkOrder()->getCustomer()->getName().' <'.$deliveryNote->getWorkOrder()->getCustomer()->getEmail().'>';
        }
        if ($deliveryNote->getWorkOrder()->getWindfarm() && $deliveryNote->getWorkOrder()->getWindfarm()->getManager()) {
            $availableMails[$deliveryNote->getWorkOrder()->getWindfarm()->getManager()->getEmail()] = $deliveryNote->getWorkOrder()->getWindfarm()->getManagerFullname().' <'.$deliveryNote->getWorkOrder()->getWindfarm()->getManager()->getEmail().'>';
        }
        if ($deliveryNote->getWorkOrder()->getCustomer()) {
            /** @var User $user */
            foreach ($deliveryNote->getWorkOrder()->getCustomer()->getContacts() as $user) {
                if ($user->isEnabled()) {
                    $availableMails[$user->getEmail()] = $user->getFullname().' <'.$user->getEmail().'>';
                }
            }
        }

        return $availableMails;
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
