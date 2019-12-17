<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class FrontController.
 *
 * @category Controller
 */
class FrontController extends Controller
{
    /**
     * @Route("/", name="front_homepage")
     *
     * @return RedirectResponse
     */
    public function homepageAction()
    {
        return $this->redirectToRoute('sonata_admin_dashboard');
    }
}
