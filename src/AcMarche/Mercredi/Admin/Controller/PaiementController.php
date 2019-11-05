<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Paiement;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Form\PaiementType;
use AcMarche\Mercredi\Admin\Form\PayerType;
use AcMarche\Mercredi\Admin\Service\Facture;
use AcMarche\Mercredi\Admin\Service\TuteurUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Paiement controller.
 *
 * @Route("/paiement")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class PaiementController extends AbstractController
{
    /**
     * Lists all Paiement entities.
     *
     * @Route("/", name="paiement", methods={"GET"})
     */
    public function index()
    {
        return $this->redirectToRoute('home_admin');
    }

    /**
     * Creates a form to create a Paiement entity.
     *
     * @param Paiement $entity The entity
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createCreateForm(Paiement $entity)
    {
        $form = $this->createForm(
            PaiementType::class,
            $entity,
            [
                'action' => $this->generateUrl('paiement_new', ['id' => $entity->getTuteur()->getId()]),
                'em' => $this->getDoctrine()->getManager(),
                'method' => 'POST',
                'paiement' => $entity,
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Create']);

        return $form;
    }

    /**
     * Displays a form to create a new Paiement entity.
     *
     * @Route("/new/{id}", name="paiement_new", methods={"GET","POST"})
     *
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function new(Tuteur $tuteur, Request $request)
    {
        $entity = new Paiement();
        $em = $this->getDoctrine()->getManager();

        $enfants = TuteurUtils::hasEnfants($tuteur);

        if (count($enfants) < 1) {
            $this->addFlash('error', "Ce parent n'a aucun enfant attribué");

            return $this->redirectToRoute('tuteur_show', ['slugname' => $tuteur->getSlugname()]);
        }

        $entity->setTuteur($tuteur);

        $form = $this->createCreateForm($entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $entity->setUserAdd($user);

            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', 'Le paiement a bien été ajouté');

            $tuteur = $entity->getTuteur();

            return $this->redirectToRoute('tuteur_show', ['slugname' => $tuteur->getSlugname()]);
        }

        return $this->render(
            'admin/paiement/new.html.twig',
            [
                'entity' => $entity,
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Paiement entity.
     *
     * @Route("/{id}", name="paiement_show", methods={"GET"})
     */
    public function show(Paiement $paiement)
    {
        $deleteForm = $this->createDeleteForm($paiement->getId());

        return $this->render(
            'admin/paiement/show.html.twig',
            [
                'entity' => $paiement,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to delete a Paiement entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('paiement_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Paiement entity.
     *
     * @Route("/{id}/edit", name="paiement_edit", methods={"GET","PUT"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function edit(Paiement $paiement, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createEditForm($paiement);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            $tuteur = $paiement->getTuteur();

            $this->addFlash('success', 'Le paiement a bien été modifié');

            return $this->redirectToRoute('tuteur_show', ['slugname' => $tuteur->getSlugname()]);
        }

        return $this->render(
            'admin/paiement/edit.html.twig',
            [
                'entity' => $paiement,
                'edit_form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to edit a Paiement entity.
     *
     * @param Paiement $entity The entity
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createEditForm(Paiement $entity)
    {
        $form = $this->createForm(
            PaiementType::class,
            $entity,
            [
                'action' => $this->generateUrl('paiement_edit', ['id' => $entity->getId()]),
                'em' => $this->getDoctrine()->getManager(),
                'method' => 'PUT',
                'paiement' => $entity,
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Update']);

        return $form;
    }

    /**
     * Deletes a Paiement entity.
     *
     * @Route("/{id}", name="paiement_delete", methods={"DELETE"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function delete(Request $request, Paiement $paiement)
    {
        $form = $this->createDeleteForm($paiement->getId());
        $form->handleRequest($request);

        $tuteur = $paiement->getTuteur();
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($paiement);
            $em->flush();

            $this->addFlash('success', 'Le paiement a bien été supprimé');
        }

        return $this->redirectToRoute('tuteur_show', ['slugname' => $tuteur->getSlugname()]);
    }

    /**
     * Payer plusieurs jours de presences avec un paiement.
     *
     * @Route("/{id}/payer", name="paiement_payer", methods={"GET","PUT"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function payer(Request $request, Paiement $paiement, Facture $facture)
    {
        $em = $this->getDoctrine()->getManager();
        $presencesOld = clone $paiement->getPresences();

        $presences = $em->getRepository(Presence::class)->getPresencesNonPayes(
            [
                'enfant_id' => $paiement->getEnfant()->getId(),
                'tuteur_id' => $paiement->getTuteur()->getId(),
                'paiement' => $paiement,
                'result' => true,
            ]
        );

        foreach ($presences as $presence) {
            $facture->handlePresence($presence);
        }

        $form = $this->createPayerForm($paiement, $presences);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $presences = $form->getData()->getPresences();
            $change = false;

            /*
             * en moins
             */
            foreach ($presencesOld as $presence) {
                if (!$presences->contains($presence)) {
                    $change = true;
                    $presence->setPaiement(null);
                    $em->persist($presence);
                }
            }

            /*
             * en plus
             */
            foreach ($presences as $presence) {
                if (!$presencesOld->contains($presence)) {
                    $change = true;
                    $presence->setPaiement($paiement);
                    $em->persist($presence);
                }
            }

            if ($change) {
                $em->flush();
                $this->addFlash('success', 'Le paiement a bien été modifié');
            } else {
                $this->addFlash('warning', 'Aucun changement effectué');
            }

            return $this->redirectToRoute('paiement_show', ['id' => $paiement->getId()]);
        }

        return $this->render(
            'admin/paiement/payer.html.twig',
            [
                'entity' => $paiement,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param $presences
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createPayerForm(Paiement $entity, $presences)
    {
        $form = $this->createForm(
            PayerType::class,
            $entity,
            [
                'action' => $this->generateUrl('paiement_payer', ['id' => $entity->getId()]),
                'method' => 'PUT',
                'paiement' => $entity,
                'presences' => $presences,
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Update']);

        return $form;
    }
}
