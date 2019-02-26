<?php

namespace AcMarche\Mercredi\Plaine\Controller;

use AcMarche\Mercredi\Plaine\Entity\Plaine;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Component\HttpFoundation\Request;
use AcMarche\Mercredi\Plaine\Form\Search\SearchPlaineType;

/**
 * Archive controller.
 *
 * @Route("/archive")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class ArchiveController extends AbstractController
{
    /**
     * Lists all Plaine entities.
     *
     * @Route("/plaines", name="archive_plaines", methods={"GET"})
     */
    public function plaines(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $data = array('archive' => 1);

        if ($session->has("plaine_archive_search")) {
            $data = unserialize($session->get("plaine_archive_search"));
        }

        $search_form = $this->createForm(SearchPlaineType::class, $data, array(
            'action' => $this->generateUrl('archive_plaines'),
            'method' => 'GET',
        ));

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
            if ($search_form->get('raz')->isClicked()) {
                $session->remove("plaine_archive_search");
                $this->addFlash('success', 'La recherche a bien été réinitialisée.');
                return $this->redirectToRoute('archive_plaines');
            }
        }

        $session->set('plaine_archive_search', serialize($data));
        $entities = $em->getRepository(Plaine::class)->search($data);

        return $this->render('plaine/archive/plaines.html.twig', array(
            'search_form' => $search_form->createView(),
            'entities' => $entities,
        ));
    }
}
