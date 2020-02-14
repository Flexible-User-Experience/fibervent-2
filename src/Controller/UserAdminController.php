<?php

namespace App\Controller;

use App\Entity\PresenceMonitoring;
use App\Entity\User;
use App\Entity\WorkerTimesheet;
use App\Enum\MonthsEnum;
use App\Form\Type\UserOperatorChooseYearAndMonthPresenceMonitoring;
use App\Form\Type\UserOperatorChooseYearAndMonthWorkerTimesheet;
use App\Form\Type\UserProfileFormType;
use App\Manager\PresenceMonitoringManager;
use App\Manager\WorkerTimesheetManager;
use App\Service\PresenceMonitoringPdfBuilderService;
use App\Service\WorkerTimesheetPdfBuilderService;
use Exception;
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
     * @throws Exception
     */
    public function buildPresenceMonitoringAction(Request $request, $id)
    {
        $showPdfPreview = false;
        $pmitems = array();
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
            $pdf->Output($this->getDestPdfFilePath($operator, 'registro-diario', $pmitems), 'F');
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
                'pdf_short_path' => $this->getShortPdfFilePath($operator, 'registro-diario', $pmitems),
            )
        );
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return Response
     * @throws Exception
     */
    public function buildWorkerTimesheetAction(Request $request, $id)
    {
        $showPdfPreview = false;
        $pmitems = array();
        /** @var User $operator */
        $operator = $this->admin->getObject($id);
        if (!$operator) {
            throw $this->createAccessDeniedException('This operator does not exisits.');
        }
        $form = $this->createForm(UserOperatorChooseYearAndMonthWorkerTimesheet::class, $operator);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var WorkerTimesheetManager $wtm */
            $wtm = $this->get('app.manager_worker_timesheet');
            $pmitems = $wtm->createFullMonthItemsListByOperatorYearAndMonth($operator, $form->get('year')->getData(), $form->get('month')->getData());
            /** @var WorkerTimesheetPdfBuilderService $wtbs */
            $wtbs = $this->get('app.worker_timesheet_pdf_builder');
            $pdf = $wtbs->build($operator, $pmitems);
            $pdf->Output($this->getDestPdfFilePath($operator, 'parte-de-trabajo', $pmitems), 'F');
            $showPdfPreview = true;
        }

        return $this->renderWithExtraParams(
            'Admin/User/build_worker_timesheet.html.twig',
            array(
                'action' => 'show',
                'object' => $operator,
                'form' => $form->createView(),
                'elements' => $this->admin->getShow(),
                'show_pdf_preview' => $showPdfPreview,
                'pdf_short_path' => $this->getShortPdfFilePath($operator, 'parte-de-trabajo', $pmitems),
            )
        );
    }

    /**
     * @param User       $user
     * @param string     $filenamePrefix
     * @param array|null $items
     *
     * @return string
     */
    private function getDestPdfFilePath(User $user, string $filenamePrefix, $items = null)
    {
        $krd = $this->getParameter('kernel.project_dir');

        return $krd.DIRECTORY_SEPARATOR.'public'.$this->getShortPdfFilePath($user, $filenamePrefix, $items);
    }

    /**
     * @param User       $user
     * @param string     $filenamePrefix
     * @param array|null $items
     * @param bool       $fromFristItem
     *
     * @return string
     */
    private function getShortPdfFilePath(User $user, string $filenamePrefix, $items = null, $fromFristItem = true)
    {
        return DIRECTORY_SEPARATOR.'pdfs'.DIRECTORY_SEPARATOR.$filenamePrefix.'-'.$user->getId().'-'.$this->getPeriodSluggedName($items).'.pdf';
    }

    /**
     * @param array|null $items
     * @param bool       $fromFristItem
     *
     * @return string
     */
    private function getPeriodSluggedName($items, $fromFristItem = true)
    {
        $ts = $this->get('translator');
        $periodString = '';
        $itemsCount = count($items);
        if ($itemsCount > 0) {
            /** @var PresenceMonitoring|WorkerTimesheet $searchedItem */
            if ($fromFristItem) {
                $searchedItem = $items[0];
            } else {
                $searchedItem = $items[$itemsCount - 1];
            }
            if ($searchedItem instanceof PresenceMonitoring) {
                $searchedItem = $searchedItem->getDate();
            }
            if ($searchedItem instanceof WorkerTimesheet) {
                $searchedItem = $searchedItem->getDeliveryNote()->getDate();
            }
            $periodString = strtolower($ts->trans(MonthsEnum::getOldMonthEnumArray()[intval($searchedItem->format('n'))])).'_'.$searchedItem->format('Y');
        }

        return $periodString;
    }

    /**
     * @param array|null $items
     *
     * @return string
     */
    private function getPeriodSluggedNameFromFirstItem($items)
    {
        return $this->getPeriodSluggedName($items, true);
    }

    /**
     * @param array|null $items
     *
     * @return string
     */
    private function getPeriodSluggedNameFromLastItem($items)
    {
        return $this->getPeriodSluggedName($items, false);
    }
}
