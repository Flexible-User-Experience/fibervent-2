<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserOperatorChooseYearAndMonthPresenceMonitoring;
use App\Form\Type\UserProfileFormType;
use App\Manager\PresenceMonitoringManager;
use App\Repository\PresenceMonitoringRepository;
use App\Service\PresenceMonitoringPdfBuilderService;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CustomerAdminController.
 *
 * @category Controller
 */
class UserAdminController extends AbstractBaseAdminController
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
    public function profileAction(Request $request = null)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw $this->createAccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->createForm(UserProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // update user profile changes
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            // build flash message
            $this->addFlash('success', $this->trans('admin.user.profile_flash'));

            return $this->redirectToRoute('sonata_admin_dashboard');
        }

        return $this->renderWithExtraParams(
            'Admin/User/profile.html.twig',
            array(
                'action' => 'show',
                'object' => $user,
                'form' => $form->createView(),
                'elements' => $this->admin->getShow(),
            )
        );
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return Response
     * @throws \Exception
     */
    public function buildPresenceMonitoringAction(Request $request, $id)
    {
        $showPdfPreview = false;
        /** @var User $operator */
        $operator = $this->admin->getObject($id);
        if (!$operator) {
            throw $this->createAccessDeniedException('This operator does not exisits.');
        }
        $form = $this->createForm(UserOperatorChooseYearAndMonthPresenceMonitoring::class, $operator);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PresenceMonitoringManager $pmm */
            $pmm = $this->get('app.manager_precense_monitoring');
            $pmitems = $pmm->createFullMonthItemsListByOperatorYearAndMonth($operator, $form->get('year')->getData(), $form->get('month')->getData());
            /** @var PresenceMonitoringPdfBuilderService $pmbs */
            $pmbs = $this->get('app.presence_monitoring_pdf_builder');
            $pdf = $pmbs->build($operator, $pmitems);
            $pdf->Output($this->getDestPdfFilePath($operator), 'F');
            $showPdfPreview = true;
        }

        return $this->renderWithExtraParams(
            'Admin/User/build_presence_monitoring.html.twig',
            array(
                'action' => 'show',
                'object' => $operator,
                'form' => $form->createView(),
                'elements' => $this->admin->getShow(),
                'show_pdf_preview' => $showPdfPreview,
                'pdf_short_path' => $this->getShortPdfFilePath($operator),
            )
        );
    }

    /**
     * @param User $user
     *
     * @return string
     */
    private function getDestPdfFilePath(User $user)
    {
        $krd = $this->getParameter('kernel.project_dir');

        return $krd.DIRECTORY_SEPARATOR.'public'.$this->getShortPdfFilePath($user);
    }

    /**
     * @param User $user
     *
     * @return string
     */
    private function getShortPdfFilePath(User $user)
    {
        return DIRECTORY_SEPARATOR.'pdfs'.DIRECTORY_SEPARATOR.'Registro---'.$user->getId().'.pdf';
    }
}
