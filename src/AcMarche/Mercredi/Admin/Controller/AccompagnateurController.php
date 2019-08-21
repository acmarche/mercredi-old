<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Accompagnateur;
use AcMarche\Mercredi\Admin\Entity\Ecole;
use AcMarche\Mercredi\Admin\Form\AccompagnateurType;
use AcMarche\Mercredi\Admin\Repository\AccompagnateurRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/accompagnateur")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class AccompagnateurController extends AbstractController
{
    /**
     * @Route("/", name="admin_accompagnateur_index", methods={"GET"})
     */
    public function index(AccompagnateurRepository $accompagnateurRepository): Response
    {
        return $this->render(
            'admin/accompagnateur/index.html.twig',
            [
                'accompagnateurs' => $accompagnateurRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new/{id}", name="admin_accompagnateur_new", methods={"GET","POST"})
     */
    public function new(Request $request, Ecole $ecole): Response
    {
        $accompagnateur = new Accompagnateur();
        $accompagnateur->setEcole($ecole);

        $form = $this->createForm(AccompagnateurType::class, $accompagnateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($accompagnateur);
            $entityManager->flush();

            $this->addFlash('success', "L'accompagnateur a bien été ajouté");

            return $this->redirectToRoute('ecole_show', ['id' => $ecole->getId()]);
        }

        return $this->render(
            'admin/accompagnateur/new.html.twig',
            [
                'accompagnateur' => $accompagnateur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="admin_accompagnateur_show", methods={"GET"})
     */
    public function show(Accompagnateur $accompagnateur): Response
    {
        return $this->render(
            'admin/accompagnateur/show.html.twig',
            [
                'accompagnateur' => $accompagnateur,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="admin_accompagnateur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Accompagnateur $accompagnateur): Response
    {
        $form = $this->createForm(AccompagnateurType::class, $accompagnateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "L'accompagnateur a bien été modifié");

            return $this->redirectToRoute('admin_accompagnateur_show', ['id' => $accompagnateur->getId()]);
        }

        return $this->render(
            'admin/accompagnateur/edit.html.twig',
            [
                'accompagnateur' => $accompagnateur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="admin_accompagnateur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Accompagnateur $accompagnateur): Response
    {
        $ecole = $accompagnateur->getEcole();
        if ($this->isCsrfTokenValid('delete'.$accompagnateur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($accompagnateur);
            $entityManager->flush();
            $this->addFlash('success', "L'accompagnateur a bien été supprimé");
        }

        return $this->redirectToRoute('ecole_show', ['id' => $ecole->getId()]);
    }
}
