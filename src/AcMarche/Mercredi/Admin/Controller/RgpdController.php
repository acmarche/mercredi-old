<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Security\Entity\Group;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class RgpdController extends AbstractController
{
    /**
     * @Route("/rgpd", name="rgpd")
     */
    public function rgpd()
    {
        $em = $this->getDoctrine()->getManager();
        $groupes = $em->getRepository(Group::class)->findAll();

        return $this->render('admin/rgpd/index.html.twig', ['groupes' => $groupes]);
    }

    public function rpgd()
    {
        $data = [];
    }

    public function forgetMe()
    {
    }
}
