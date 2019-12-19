<?php

namespace App\Controller;

use App\Entity\Audit;
use App\Entity\AuditWindmillBlade;
use App\Entity\Windfarm;
use App\Enum\WindfarmLanguageEnum;
use App\Form\Type\WindfarmAnnualStatsFormType;
use App\Form\Type\WindfarmAuditStatsFormType;
use App\Service\WindfarmAuditsPdfBuilderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class WindfarmAdminController.
 *
 * @category Controller
 */
class WindfarmAdminController extends AbstractBaseAdminController
{
    /**
     * Custom show action redirect to public frontend view.
     *
     * @param null $id
     *
     * @return Response
     *
     * @throws NotFoundHttpException     If the object does not exist
     * @throws AccessDeniedHttpException If access is not granted
     */
    public function showAction($id = null)
    {
        $request = $this->resolveRequest();
        $id = $request->get($this->admin->getIdParameter());

        /** @var Windfarm $object */
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw $this->createNotFoundException(sprintf('Unable to find windfarm record with id: %s', $id));
        }

        // Customer filter
        if (!$this->get('app.auth_customer')->isWindfarmOwnResource($object)) {
            throw new AccessDeniedHttpException();
        }

        return $this->renderWithExtraParams(
            'Admin/Windfarm/show.html.twig',
            array(
                'action' => 'show',
                'object' => $object,
            )
        );
    }

    /**
     * Show windfarm audits list view.
     *
     * @param Request|null $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException     If the object does not exist
     * @throws AccessDeniedHttpException If access is not granted
     */
    public function auditsAction(Request $request = null)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var Windfarm $object */
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw $this->createNotFoundException(sprintf('Unable to find windfarm record with id: %s', $id));
        }

        // Customer filter
        if (!$this->get('app.auth_customer')->isWindfarmOwnResource($object)) {
            throw new AccessDeniedHttpException();
        }

        return $this->renderWithExtraParams(
            'Admin/Windfarm/map.html.twig',
            array(
                'action' => 'show',
                'object' => $object,
            )
        );
    }

    /**
     * Create windmills map view.
     *
     * @param Request|null $request
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

        /** @var Windfarm $object */
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw $this->createNotFoundException(sprintf('Unable to find windfarm record with id: %s', $id));
        }

        // Customer filter
        if (!$this->get('app.auth_customer')->isWindfarmOwnResource($object)) {
            throw new AccessDeniedHttpException();
        }

        return $this->renderWithExtraParams(
            'Admin/Windfarm/map.html.twig',
            array(
                'action' => 'show',
                'object' => $object,
            )
        );
    }

    /**
     * Export Windmill blades from each Wind Farm in excel format action.
     * First step = display a year choice selector from audits.
     *
     * @param Request|null $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException     If the object does not exist
     * @throws AccessDeniedHttpException If access is not granted
     */
    public function excelAction(Request $request = null)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var Windfarm $object */
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw $this->createNotFoundException(sprintf('Unable to find windfarm record with id: %s', $id));
        }

        // Customer filter
        if (!$this->get('app.auth_customer')->isWindfarmOwnResource($object)) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(WindfarmAnnualStatsFormType::class, null, array('windfarm_id' => $object->getId()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $damage_categories = null;
            if (array_key_exists('damage_categories', $request->get(WindfarmAnnualStatsFormType::BLOCK_PREFIX))) {
                $damage_categories = $request->get(WindfarmAnnualStatsFormType::BLOCK_PREFIX)['damage_categories'];
            }

            $statuses = null;
            if (array_key_exists('audit_status', $request->get(WindfarmAnnualStatsFormType::BLOCK_PREFIX))) {
                $statuses = $request->get(WindfarmAnnualStatsFormType::BLOCK_PREFIX)['audit_status'];
            }

            $year = intval($request->get(WindfarmAnnualStatsFormType::BLOCK_PREFIX)['year']);

            $audits = $this->getDoctrine()->getRepository('App:Audit')->getAuditsByWindfarmByStatusesAndYear(
                $object,
                $statuses,
                $year
            );

            /** @var Audit $audit */
            foreach ($audits as $audit) {
                $auditWindmillBlades = $audit->getAuditWindmillBlades();
                /** @var AuditWindmillBlade $auditWindmillBlade */
                foreach ($auditWindmillBlades as $auditWindmillBlade) {
                    $bladeDamages = $this->getDoctrine()->getRepository('App:BladeDamage')->getItemsOfAuditWindmillBladeSortedByRadius($auditWindmillBlade);
                    if (count($bladeDamages) > 0) {
                        $auditWindmillBlade->setBladeDamages($bladeDamages);
                    }
                }
            }

            return $this->renderWithExtraParams(
                'Admin/Windfarm/annual_stats.html.twig',
                array(
                    'action' => 'show',
                    'object' => $object,
                    'form' => $form->createView(),
                    'damage_categories' => $damage_categories,
                    'year' => $year,
                    'audits' => $audits,
                    'show_download_xls_button' => true,
                )
            );
        }

        return $this->renderWithExtraParams(
            'Admin/Windfarm/annual_stats.html.twig',
            array(
                'action' => 'show',
                'object' => $object,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Export Windmill blades from each Wind Farm in excel format action.
     * Second step = build an excel file and return as an attatchment response.
     *
     * @param Request|null $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException     If the object does not exist
     * @throws AccessDeniedHttpException If access is not granted
     * @throws \Exception
     */
    public function excelAttachmentAction(Request $request = null)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var Windfarm $object */
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw $this->createNotFoundException(sprintf('Unable to find windfarm record with id: %s', $id));
        }

        // Customer filter
        if (!$this->get('app.auth_customer')->isWindfarmOwnResource($object)) {
            throw new AccessDeniedHttpException();
        }

        $damage_categories = null;
        if (!is_null($request->get('damage_categories'))) {
            $damage_categories = explode('-', $request->get('damage_categories'));
        }

        $statuses = null;
        if (!is_null($request->get('audit_status'))) {
            $statuses = explode('-', $request->get('audit_status'));
        }

        $year = intval($request->get('year'));

        $audits = $this->getDoctrine()->getRepository('App:Audit')->getAuditsByWindfarmByStatusesAndYear(
            $object,
            $statuses,
            $year
        );

        /** @var Audit $audit */
        foreach ($audits as $audit) {
            $auditWindmillBlades = $audit->getAuditWindmillBlades();
            /** @var AuditWindmillBlade $auditWindmillBlade */
            foreach ($auditWindmillBlades as $auditWindmillBlade) {
                $bladeDamages = $this->getDoctrine()->getRepository('App:BladeDamage')->getItemsOfAuditWindmillBladeSortedByRadius($auditWindmillBlade);
                if (count($bladeDamages) > 0) {
                    $auditWindmillBlade->setBladeDamages($bladeDamages);
                }
            }
        }

        $response = $this->renderWithExtraParams(
            'Admin/Windfarm/excel.xls.twig',
            array(
                'action' => 'show',
                'windfarm' => $object,
                'audits' => $audits,
                'damage_categories' => $damage_categories,
                'year' => $year,
                'locale' => WindfarmLanguageEnum::getEnumArray()[$object->getLanguage()],
            )
        );

        $currentDate = new \DateTime();
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$currentDate->format('Y-m-d').'_'.$object->getSlug().'.xls"');

        return $response;
    }

    /**
     * Export Windfarm Audits in PDF format action.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException     If the object does not exist
     * @throws AccessDeniedHttpException If access is not granted
     */
    public function pdfAction(Request $request = null)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var Windfarm $object */
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw $this->createNotFoundException(sprintf('Unable to find windfarm record with id: %s', $id));
        }

        // Customer filter
        if (!$this->get('app.auth_customer')->isWindfarmOwnResource($object)) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(WindfarmAuditStatsFormType::class, null, array('windfarm_id' => $object->getId()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $damageCategories = $this->get('app.damage_category_repository')->findAllSortedByCategory();
            $statuses = null;
            if (array_key_exists('audit_status', $request->get(WindfarmAuditStatsFormType::BLOCK_PREFIX))) {
                $statuses = $request->get(WindfarmAuditStatsFormType::BLOCK_PREFIX)['audit_status'];
            }
            $year = intval($request->get(WindfarmAuditStatsFormType::BLOCK_PREFIX)['year']);
            $audits = $this->getDoctrine()->getRepository('App:Audit')->getAuditsByWindfarmByStatusesYearAndRange(
                $object,
                $statuses,
                $year,
                $request->get(WindfarmAuditStatsFormType::BLOCK_PREFIX)['dates_range']
            );

            return $this->renderWithExtraParams(
                'Admin/Windfarm/pdf_filter_pre_build.html.twig',
                array(
                    'action' => 'show',
                    'object' => $object,
                    'form' => $form->createView(),
                    'year' => $year,
                    'show_download_pdf_button' => true,
                    'damage_categories' => $damageCategories,
                    'audits' => $audits,
                    'turbines' => $this->get('app.windfarm_builder_bridge')->getInvolvedTurbinesInAuditsList($audits),
                    'turbine_models' => $this->get('app.windfarm_builder_bridge')->getInvolvedTurbineModelsInAuditsList($audits),
                    'blades' => $this->get('app.windfarm_builder_bridge')->getInvolvedBladesInAuditsList($audits),
                    'technicians' => $this->get('app.windfarm_builder_bridge')->getInvolvedTechniciansInAuditsList($audits),
                    'audit_types' => $this->get('app.windfarm_builder_bridge')->getInvolvedAuditTypesInAuditsList($audits),
                    'audit_dates' => $this->get('app.windfarm_builder_bridge')->getInvolvedAuditDatesInAuditsList($this->getDoctrine()->getRepository('App:Audit')->getAuditDatesForAuditsByWindfarmByStatusesYearAndRange($object, $statuses, $year, $request->get(WindfarmAuditStatsFormType::BLOCK_PREFIX)['dates_range'])),
                )
            );
        }

        return $this->renderWithExtraParams(
            'Admin/Windfarm/pdf_filter_pre_build.html.twig',
            array(
                'action' => 'show',
                'object' => $object,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Download PDF attatchment with Windfarm Audits action.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException     If the object does not exist
     * @throws AccessDeniedHttpException If access is not granted
     */
    public function pdfAttachmentAction(Request $request = null)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var Windfarm $object */
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw $this->createNotFoundException(sprintf('Unable to find windfarm record with id: %s', $id));
        }

        // Customer filter
        if (!$this->get('app.auth_customer')->isWindfarmOwnResource($object)) {
            throw new AccessDeniedHttpException();
        }

        $damageCategories = $this->get('app.damage_category_repository')->findAllSortedByCategory();

        $statuses = null;
        if ($request->get('audit_status')) {
            $statuses = explode('-', $request->get('audit_status'));
        }

        $year = intval($request->get('year'));

        $range = array();
        $range['start'] = '';
        $range['end'] = '';
        if ($request->get('date_range_start')) {
            $range['start'] = $request->get('date_range_start');
        }
        if ($request->get('date_range_end')) {
            $range['end'] = $request->get('date_range_end');
        }

        $audits = $this->getDoctrine()->getRepository('App:Audit')->getAuditsByWindfarmByStatusesYearAndRange(
            $object,
            $statuses,
            $year,
            $range
        );
        $dateRanges = $this->getDoctrine()->getRepository('App:Audit')->getAuditDatesForAuditsByWindfarmByStatusesYearAndRange($object, $statuses, $year, $range);

        /** @var WindfarmAuditsPdfBuilderService $wapbs */
        $wapbs = $this->get('app.windfarm_audits_pdf_builder');
        $pdf = $wapbs->build($object, $damageCategories, $audits, $year, $dateRanges);

        return new Response($pdf->Output('informe_auditorias_parque_eolico_'.$object->getId().'.pdf', 'I'), 200, array('Content-type' => 'application/pdf'));
    }
}
