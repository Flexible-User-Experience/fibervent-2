<?php

namespace App\Controller;

use App\Entity\WorkerTimesheet;
use App\Service\AuthCustomerService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class WorkerTimesheetAdminController.
 *
 * @category Controller
 */
class WorkerTimesheetAdminController extends AbstractBaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     * @throws AccessDeniedHttpException
     */
    public function editAction($id = null)
    {
        if (!$this->getGuardian()->isWorkerTimesheetOwnResource($this->getPersistedObject())) {
            throw new AccessDeniedHttpException();
        }

        return parent::editAction($id);
    }

    /**
     * @return WorkerTimesheet
     * @throws NotFoundHttpException
     */
    private function getPersistedObject()
    {
        $request = $this->resolveRequest();
        $id = $request->get($this->admin->getIdParameter());

        /** @var WorkerTimesheet $object */
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw $this->createNotFoundException(sprintf('Unable to find worker timesheet record with id: %s', $id));
        }

        return $object;
    }
}
