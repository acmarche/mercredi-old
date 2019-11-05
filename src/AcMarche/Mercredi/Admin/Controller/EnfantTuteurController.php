<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Form\Enfant\EnfantTuteurType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * EnfantTuteur controller.
 *
 * @Route("/enfanttuteur")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class EnfantTuteurController extends AbstractController
{
    /**
     * Displays a form to edit an existing EnfantTuteur entity.
     *
     * @Route("/{id}/edit", name="enfanttuteur_edit", methods={"GET","PUT"})
     */
    public function edit(EnfantTuteur $enfantTuteur, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createEditForm($enfantTuteur);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            $enfant = $enfantTuteur->getEnfant();

            $this->addFlash('success', 'La relation a bien été modifiée');

            return $this->redirectToRoute('enfant_show', ['slugname' => $enfant->getSlugname()]);
        }

        return $this->render('admin/enfant_tuteur/edit.html.twig', [
            'entity' => $enfantTuteur,
            'edit_form' => $editForm->createView(),
        ]);
    }

    /**
     * Creates a form to edit a EnfantTuteur entity.
     *
     * @param EnfantTuteur $entity The entity
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createEditForm(EnfantTuteur $entity)
    {
        $form = $this->createForm(EnfantTuteurType::class, $entity, [
            'action' => $this->generateUrl('enfanttuteur_edit', ['id' => $entity->getId()]),
            'method' => 'PUT',
        ]);

        $form->add('submit', SubmitType::class, ['label' => 'Update']);

        return $form;
    }
}
