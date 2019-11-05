<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Admin\Form\Search\SearchEnfantType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Archive controller.
 *
 * @Route("/archive")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class ArchiveController extends AbstractController
{
    /**
     * List archive enfants.
     *
     * @Route("/enfants", name="archive_enfants", methods={"GET"})
     */
    public function enfants(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $data = ['archive' => 1];

        if ($session->has('archive_enfant_search')) {
            $data = unserialize($session->get('archive_enfant_search'));
        }

        $search_form = $this->createForm(
            SearchEnfantType::class,
            $data,
            [
                'method' => 'GET',
            ]
        );

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
            if ($search_form->get('raz')->isClicked()) {
                $session->remove('archive_enfant_search');
                $this->addFlash('success', 'La recherche a bien été réinitialisée.');

                return $this->redirectToRoute('archive_enfants');
            }
        }

        $session->set('archive_enfant_search', serialize($data));
        $entities = $em->getRepository(Enfant::class)->search($data);

        return $this->render(
            'admin/archive/enfants.html.twig',
            [
                'search_form' => $search_form->createView(),
                'entities' => $entities,
            ]
        );
    }

    /**
     * Lists all Jour entities.
     *
     * @Route("/jours", name="archive_jours", methods={"GET"})
     */
    public function jours()
    {
        $em = $this->getDoctrine()->getManager();

        $args = ['order' => ['j.date_jour', 'DESC'], 'archive' => 1];
        $entities = $em->getRepository(Jour::class)->search($args);

        return $this->render(
            'admin/archive/jours.html.twig',
            [
                'entities' => $entities,
            ]
        );
    }
}
