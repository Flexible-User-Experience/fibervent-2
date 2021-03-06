<?php

namespace App\Controller;

use App\Entity\Audit;
use App\Entity\User;
use App\Form\Type\AuditEmailSendFormType;
use App\Manager\WorkOrderManager;
use App\Service\AuditPdfBuilderService;
use Exception;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Router;

/**
 * Class AuditAdminController.
 *
 * @category Controller
 */
class AuditAdminController extends AbstractBaseAdminController
{
    /**
     * Export Audit in PDF format action.
     *
     * @return Response
     *
     * @throws NotFoundHttpException     If the object does not exist
     * @throws AccessDeniedHttpException If access is not granted
     * @throws Exception
     */
    public function pdfAction()
    {
        /** @var Audit $object */
        $object = $this->getPersistedObject();

        /** @var AuditPdfBuilderService $apbs */
        $apbs = $this->get('app.audit_pdf_builder');
        $pdf = $apbs->build($object);

        return new Response($pdf->Output('informe_auditoria_'.$object->getId().'.pdf', 'I'), 200, array('Content-type' => 'application/pdf'));
    }

    /**
     * Custom show action redirect to public frontend view.
     *
     * @param null|int $id
     *
     * @return Response
     *
     * @throws NotFoundHttpException     If the object does not exist
     * @throws AccessDeniedHttpException If access is not granted
     */
    public function showAction($id = null)
    {
        /** @var Audit $object */
        $object = $this->getPersistedObject();

        // Customer filter
        if (!$this->get('app.auth_customer')->isAuditOwnResource($object)) {
            throw new AccessDeniedHttpException();
        }

        $sortedBladeDamages = array();
        $bdr = $this->get('app.blade_damage_repository');
        foreach ($object->getAuditWindmillBlades() as $auditWindmillBlade) {
            array_push($sortedBladeDamages, $bdr->getItemsOfAuditWindmillBladeSortedByRadius($auditWindmillBlade));
        }

        return $this->renderWithExtraParams(
            'Admin/Audit/show.html.twig',
            array(
                'action' => 'show',
                'object' => $object,
                'sortedBladeDamages' => $sortedBladeDamages,
            )
        );
    }

    /**
     * Custom email action.
     *
     * @param Request|null $request
     *
     * @return Response
     *
     * @throws Exception
     */
    public function emailAction(Request $request = null)
    {
        /** @var Audit $object */
        $object = $this->getPersistedObject();

        /** @var AuditPdfBuilderService $apbs */
        $apbs = $this->get('app.audit_pdf_builder');
        $pdf = $apbs->build($object);
        $pdf->Output($this->getDestAuditFilePath($object), 'F');

        $form = $this->createForm(AuditEmailSendFormType::class, $object, array(
            'default_subject' => 'Resultado inspección Fibervent',
            'default_msg' => 'Adjunto archivo resultado auditoria número '.$object->getId(),
            'to_emails_list' => $this->getReversedToEmailsList($object),
            'cc_emails_list' => $this->getReversedCcEmailsList($object),
        ));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $to = $form->get('to')->getData();
            $cc = $form->get('cc')->getData();
            $this->get('app.notification')->deliverAuditEmail($form, $this->getDestAuditFilePath($object));
            $this->addFlash('sonata_flash_success', 'La auditoria núm. '.$object->getId().' se ha enviado correctamente a '.$to.($cc ? ' con copia para '.$cc : ''));

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->renderWithExtraParams(
            'Admin/Audit/email.html.twig',
            array(
                'action' => 'show',
                'object' => $object,
                'form' => $form->createView(),
                'pdf_short_path' => $this->getShortAuditFilePath($object),
            )
        );
    }

    /**
     * @param ProxyQueryInterface $selectedModelQuery
     * @param Request|null        $request
     *
     * @return Response|RedirectResponse
     */
    public function batchActionCreateWorkOrder(ProxyQueryInterface $selectedModelQuery, Request $request = null)
    {
        /** @var Router $routing */
        $routing = $this->container->get('router');
        $this->admin->checkAccess('edit');
        $selectedModels = $selectedModelQuery->execute();
        try {
            /** @var WorkOrderManager $workOrderManager */
            $workOrderManager = $this->container->get('app.manager_work_order');
            if ($workOrderManager->checkIfAllAuditsBelongToOneWindfarm($selectedModels)) {
                $workOrder = $workOrderManager->createWorkOrderFromAuditsForDamageCategoryLevel($selectedModels, intval($request->get('damage_category_level')));
            } else {
                $this->addFlash('error', 'Error al generar el proyecto. Las auditorias no pertenecen a un mismo parque eólico.');

                return new RedirectResponse(
                    $this->admin->generateUrl('list', [
                        'filter' => $this->admin->getFilterParameters(),
                    ])
                );
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error al generar el proyecto.');
            $this->addFlash('error', $e->getMessage());

            return new RedirectResponse(
                $this->admin->generateUrl('list', [
                    'filter' => $this->admin->getFilterParameters(),
                ])
            );
        }

        return new RedirectResponse(
            $routing->generate('admin_app_workorder_edit', ['id' => $workOrder->getId()])
        );
    }

    /**
     * @param Audit $audit
     *
     * @return string
     */
    private function getDestAuditFilePath(Audit $audit)
    {
        $krd = $this->getParameter('kernel.project_dir');

        return $krd.DIRECTORY_SEPARATOR.'public'.$this->getShortAuditFilePath($audit);
    }

    /**
     * @param Audit $audit
     *
     * @return string
     */
    private function getShortAuditFilePath(Audit $audit)
    {
        return DIRECTORY_SEPARATOR.'pdfs'.DIRECTORY_SEPARATOR.'Auditoria-'.$audit->getId().'.pdf';
    }

    /**
     * @param Audit $audit
     *
     * @return array
     */
    private function getToEmailsList(Audit $audit)
    {
        return $this->commonEmailsList($audit);
    }

    /**
     * @param Audit $audit
     *
     * @return array
     */
    private function getReversedToEmailsList(Audit $audit)
    {
        return array_flip($this->commonEmailsList($audit));
    }

    /**
     * @param Audit $audit
     *
     * @return array
     */
    private function getCcEmailsList(Audit $audit)
    {
        $availableMails = $this->commonEmailsList($audit);
        $users = $this->get('app.user_repository')->findOnlyAvailableSortedByName($audit->getCustomer());
        /** @var User $user */
        foreach ($users as $user) {
            $availableMails[$user->getEmail()] = $user->getFullname().' <'.$user->getEmail().'>';
        }

        return $availableMails;
    }

    /**
     * @param Audit $audit
     *
     * @return array
     */
    private function getReversedCcEmailsList(Audit $audit)
    {
        return array_flip($this->getCcEmailsList($audit));
    }

    /**
     * @param Audit $audit
     *
     * @return array
     */
    private function commonEmailsList(Audit $audit)
    {
        $availableMails = array();
        if ($audit->getCustomer()->getEmail()) {
            $availableMails[$audit->getCustomer()->getEmail()] = $audit->getCustomer()->getName().' <'.$audit->getCustomer()->getEmail().'>';
        }
        if ($audit->getWindfarm() && $audit->getWindfarm()->getManager()) {
            $availableMails[$audit->getWindfarm()->getManager()->getEmail()] = $audit->getWindfarm()->getManagerFullname().' <'.$audit->getWindfarm()->getManager()->getEmail().'>';
        }
        if ($audit->getCustomer()) {
            /** @var User $user */
            foreach ($audit->getCustomer()->getContacts() as $user) {
                if ($user->isEnabled()) {
                    $availableMails[$user->getEmail()] = $user->getFullname().' <'.$user->getEmail().'>';
                }
            }
        }

        return $availableMails;
    }

    /**
     * @return Audit
     * @throws NotFoundHttpException
     */
    private function getPersistedObject()
    {
        $request = $this->resolveRequest();
        $id = $request->get($this->admin->getIdParameter());

        /** @var Audit $object */
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw $this->createNotFoundException(sprintf('Unable to find audit record with id: %s', $id));
        }

        return $object;
    }
}
