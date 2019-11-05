<?php

namespace AcMarche\Mercredi\Plaine\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home_plaine")
     */
    public function index()
    {
        return $this->redirectToRoute('plaine');
    }
}
