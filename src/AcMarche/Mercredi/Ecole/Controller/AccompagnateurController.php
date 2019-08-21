<?php

namespace AcMarche\Mercredi\Ecole\Controller;

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
 * @Route("/ecole/accompagnateur")
 * @IsGranted("ROLE_MERCREDI_ECOLE")
 */
class AccompagnateurController extends AbstractController
{
    /**
     * @Route("/", name="ecole_accompagnateur_index", methods={"GET"})
     * @IsGranted("index_accompagnateur")
     */
    public function index(AccompagnateurRepository $accompagnateurRepository): Response
    {
        $user = $this->getUser();
        $ecoles = $user->getEcoles();

        $accompagnateurs = $accompagnateurRepository->findByEcoles($ecoles);

        return $this->render(
            'ecole/accompagnateur/index.html.twig',
            [
                'accompagnateurs' => $accompagnateurs,
            ]
        );
    }

    /**
     * @Route("/new/{id}", name="ecole_accompagnateur_new", methods={"GET","POST"})
     * @IsGranted("edit", subject="ecole")
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

            return $this->redirectToRoute('ecole_accompagnateur_show', ['id' => $accompagnateur->getId()]);
        }

        return $this->render(
            'ecole/accompagnateur/new.html.twig',
            [
                'accompagnateur' => $accompagnateur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="ecole_accompagnateur_show", methods={"GET"})
     * @IsGranted("show", subject="accompagnateur")
     */
    public function show(Accompagnateur $accompagnateur): Response
    {
        return $this->render(
            'ecole/accompagnateur/show.html.twig',
            [
                'accompagnateur' => $accompagnateur,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="ecole_accompagnateur_edit", methods={"GET","POST"})
     * @IsGranted("edit", subject="accompagnateur")
     */
    public function edit(Request $request, Accompagnateur $accompagnateur): Response
    {
        $form = $this->createForm(AccompagnateurType::class, $accompagnateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "L'accompagnateur a bien été modifié");

            return $this->redirectToRoute('ecole_accompagnateur_show', ['id' => $accompagnateur->getId()]);
        }

        return $this->render(
            'ecole/accompagnateur/edit.html.twig',
            [
                'accompagnateur' => $accompagnateur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="ecole_accompagnateur_delete", methods={"DELETE"})
     * @IsGranted("delete", subject="accompagnateur")
     */
    public function delete(Request $request, Accompagnateur $accompagnateur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$accompagnateur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($accompagnateur);
            $entityManager->flush();
            $this->addFlash('success', "L'accompagnateur a bien été supprimé");
        }

        return $this->redirectToRoute('ecole_accompagnateur_index');
    }
}
