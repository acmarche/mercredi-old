<?php

namespace AcMarche\Mercredi\Plaine\Controller;

use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Plaine\Entity\PlaineJour;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Ajax controller.
 *
 * @Route("/ajaxplaine")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class AjaxController extends AbstractController
{
    /**
     * @Route("/getjours", name="ajax_get_jours")
     */
    public function getJours(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $plaineId = $request->get('plaineId');
        $plaine = $em->getRepository(Plaine::class)->find($plaineId);

        if (!$plaine) {
            $content = 'Plaine non trouvée';
        } else {
            $jours = $em->getRepository(PlaineJour::class)->search(['plaine' => $plaine]);
            $content = $this->renderView('plaine/ajax/jours.html.twig', ['jours' => $jours]);
        }
        $response = new Response();
        $response->setStatusCode(200);
        $response->setContent($content);

        return $response;
    }
}
