<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Windmill;
use App\Entity\WorkOrder;
use App\Model\AjaxResponse;
use App\Repository\CustomerRepository;
use App\Repository\WindfarmRepository;
use App\Repository\WindmillBladeRepository;
use App\Repository\WindmillRepository;
use App\Service\WorkOrderPdfBuilderService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        $ajaxResponse = new AjaxResponse();
        /** @var CustomerRepository $cr */
        $cr = $this->container->get('app.customer_repository');
        /** @var WindfarmRepository $em */
        $wfr = $this->container->get('app.windfarm_repository');
        /** @var Customer $customer */
        $customer = $cr->find($id);
        if (!$customer) {
            return new JsonResponse($ajaxResponse);
        }
        $ajaxResponse->setData($wfr->findCustomerEnabledSortedByNameAjax($customer));
        $jsonEncodedResult = $ajaxResponse->getJsonEncodedResultWithoutFirstOptionSelected();

        return new JsonResponse($jsonEncodedResult);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getWindmillsFromSelectedWindfarmsIdsAction(Request $request) {
        $data = [];
        $windmfarmsIds = $request->query->all();
        $ajaxResponse = new AjaxResponse();
        /** @var WindmillRepository $wmbr */
        $wmr = $this->container->get('app.windmill_repository');
        if (count($windmfarmsIds) > 0) {
            $data = $wmr->getMultipleByWindfarmsIdsArrayAjax($windmfarmsIds);
        }
        $ajaxResponse->setData($data);
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
        $ajaxResponse = new AjaxResponse();
        /** @var WindmillRepository $wmr */
        $wmr = $this->container->get('app.windmill_repository');
        /** @var WindmillBladeRepository $wmbr */
        $wmbr = $this->container->get('app.windmill_blade_repository');
        /** @var Windmill $windmill */
        $windmill = $wmr->find($id);
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
     * @throws Exception
     */
    public function pdfAction()
    {
        /** @var WorkOrder $object */
        $object = $this->getPersistedObject();
        /** @var WorkOrderPdfBuilderService $apbs */
        $apbs = $this->get('app.workorder_pdf_builder');
        $pdf = $apbs->build($object);

        return new Response($pdf->Output('informe_proyecto_'.$object->getId().'.pdf', 'I'), 200, array('Content-type' => 'application/pdf'));
    }

    public function uploadWorkOrderTaskFileAction()
    {

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
            throw $this->createNotFoundException(sprintf('Unable to find work order record with id: %s', $id));
        }

        return $object;
    }
}
