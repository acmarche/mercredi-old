<?php

namespace AcMarche\Mercredi\Animateur\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_MERCREDI_ANIMATEUR")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home_animateur")
     */
    public function index()
    {
        return $this->render(
            'animateur/default/index.html.twig'
        );
    }
}
