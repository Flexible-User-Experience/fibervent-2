<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Windfarm;
use App\Entity\Windmill;
use App\Entity\WorkOrder;
use App\Entity\WorkOrderTask;
use App\Model\AjaxResponse;
use App\Repository\CustomerRepository;
use App\Repository\WindfarmRepository;
use App\Repository\WindmillBladeRepository;
use App\Repository\WindmillRepository;
use App\Repository\WorkOrderRepository;
use App\Service\WorkOrderPdfBuilderService;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Symfony\Component\HttpFoundation\File\Exception\NoFileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @param int $woid WorOrder ID
     * @param int $wfid Windfarm ID
     *
     * @return JsonResponse
     */
    public function getWindmillsFromWorkOrderIdAndWindfarmIdAction($woid, $wfid) {
        $data = [];
        $ajaxResponse = new AjaxResponse();
        if ($woid && $wfid) {
            /** @var WorkOrderRepository $wor */
            $wor = $this->container->get('app.work_order_repository');
            /** @var WorkOrder $workOrder */
            $workOrder = $wor->find($woid);
            /** @var WindmillRepository $wfr */
            $wfr = $this->container->get('app.windfarm_repository');
            /** @var Windfarm $windfarm */
            $windfarm = $wfr->find($wfid);
            if ($workOrder && $windfarm) {
                $windmfarmsIds = [];
                $windmfarmsIds[] = $windfarm->getId();
                /** @var WindmillRepository $wmbr */
                $wmr = $this->container->get('app.windmill_repository');
                $results = $wmr->getMultipleByWindfarmsIdsArrayAjax($windmfarmsIds);
                /** @var WorkOrderTask $workOrderTask */
                foreach ($workOrder->getWorkOrderTasks() as $workOrderTask) {
                    /** @var array $result */
                    foreach ($results as $result) {
                        if ($workOrderTask->getWindmill()->getId() == $result['id'] && $this->isNewElement($data, $result['id'])) {
                            $data[] = $result;
                        }
                    }
                }
            }
        }
        $ajaxResponse->setData($data);
        $jsonEncodedResult = $ajaxResponse->getJsonEncodedResult();

        return new JsonResponse($jsonEncodedResult);
    }

    /**
     * @param array $data
     * @param int   $id
     *
     * @return bool
     */
    private function isNewElement($data, $id)
    {
        $isNew = true;
        /** @var array $item */
        foreach ($data as $item) {
            if ($item['id'] == $id) {
                $isNew = false;
                break;
            }
        }

        return $isNew;
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

    /**
     * @param Request $request      WorkOrder task ID
     * @param int     $id           WorkOrder ID
     * @param int     $filerowindex WorkOrderTask file row index
     *
     * @return JsonResponse
     *
     * @throws EntityNotFoundException
     * @throws NoFileException
     */
    public function uploadWorkOrderTaskFileAction(Request $request, $id, $filerowindex)
    {
        /** @var WorkOrder $object */
        $object = $this->getPersistedObject();
        if (!$object) {
            throw new EntityNotFoundException();
        }
        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        if (!$file) {
            throw new NoFileException();
        }
        if (count($object->getWorkOrderTasks()) >= ($filerowindex + 1)) {
            // is related with an existing WorkOrderTask
            /** @var WorkOrderTask $selectedWorkOrderTask */
            $selectedWorkOrderTask = $object->getWorkOrderTasks()[$filerowindex];
        } else {
            // is related with an undefined (new) WorkOrderTask
            /** @var WorkOrderTask $selectedWorkOrderTask */
            $selectedWorkOrderTask = new WorkOrderTask();
            $selectedWorkOrderTask->setFakeId(-1);
            $selectedWorkOrderTask->setDescription('no description');
        }

        return new JsonResponse([
            'hit' => 'me',
            'filename' => $file->getFilename(),
            'selected_work_order_task_id' => $selectedWorkOrderTask->getId(),
            'selected_work_order_task_description' => $selectedWorkOrderTask->getDescription(),
        ]);
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
