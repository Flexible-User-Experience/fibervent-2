<?php

namespace App\Controller;

use App\Form\Type\UserOperatorChooseYearAndMonthPresenceMonitoring;
use App\Form\Type\UserProfileFormType;
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
     * @param int $id
     *
     * @return Response
     */
    public function buildPresenceMonitoringAction($id)
    {
        $operator = $this->admin->getObject($id);
        if (!$operator) {
            throw $this->createAccessDeniedException('This operator does not exisits.');
        }
        $form = $this->createForm(UserOperatorChooseYearAndMonthPresenceMonitoring::class, $operator);

        return $this->renderWithExtraParams(
            'Admin/User/build_presence_monitoring.html.twig',
            array(
                'action' => 'show',
                'object' => $operator,
                'form' => $form->createView(),
                'elements' => $this->admin->getShow(),
            )
        );
    }
}
