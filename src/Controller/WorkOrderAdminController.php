<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Windfarm;
use App\Entity\Windmill;
use App\Entity\WindmillBlade;
use App\Entity\WorkOrder;
use App\Model\AjaxResponse;
use App\Repository\WindfarmRepository;
use App\Repository\WindmillBladeRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class WorkOrderAdminController.
 *
 * @category Controller
 */
class WorkOrderAdminController extends AbstractBaseAdminController
{
    /**
     * @param int $id Customer ID
     *
     * @return JsonResponse
     */
    public function getWindfarmsFromCustomerIdAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');
        /** @var Customer $customer */
        $customer = $em->getRepository(Customer::class)->find($id);
        /** @var WindfarmRepository $wfr */
        $wfr = $em->getRepository(Windfarm::class);

        $ajaxResponse = new AjaxResponse();
        if (!$customer) {
            return new JsonResponse($ajaxResponse);
        }

        $ajaxResponse->setData($wfr->findCustomerEnabledSortedByNameAjax($customer));
        $jsonEncodedResult = $ajaxResponse->getJsonEncodedResult();

        return new JsonResponse($jsonEncodedResult);
    }

    /**
     * @param int $id Customer ID
     *
     * @return JsonResponse
     */
    public function getWindmillbladesFromWindmillIdAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');
        /** @var Windmill $windmill */
        $windmill = $em->getRepository(Windmill::class)->find($id);
        /** @var WindmillBladeRepository $wmbr */
        $wmbr = $em->getRepository(WindmillBlade::class);

        $ajaxResponse = new AjaxResponse();
        if (!$windmill) {
            return new JsonResponse($ajaxResponse);
        }
        $ajaxResponse->setData($wmbr->findWindmillSortedByCodeAjax($windmill));
        $jsonEncodedResult = $ajaxResponse->getJsonEncodedResult();

        return new JsonResponse($jsonEncodedResult);
    }

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
        /** @var WorkOrder $object */
        $object = $this->getPersistedObject();

        /** @var WorkOrderPdfBuilderService $apbs */
        $apbs = $this->get('app.workorder_pdf_builder');
        $pdf = $apbs->build();

        return new Response($pdf->Output('informe_proyecto_'.$object->getId().'.pdf', 'I'), 200, array('Content-type' => 'application/pdf'));
    }

    /**
     * @return WorkOrder
     * @throws NotFoundHttpException
     */
    private function getPersistedObject()
    {
        $request = $this->resolveRequest();
        $id = $request->get($this->admin->getIdParameter());

        /** @var WorkOrder $object */
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw $this->createNotFoundException(sprintf('Unable to find audit record with id: %s', $id));
        }

        return $object;
    }
}
