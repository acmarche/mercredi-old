<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Form\Jour\JourAnimateursType;
use AcMarche\Mercredi\Admin\Form\JourType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Jour controller.
 *
 * @Route("/jour")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class JourController extends AbstractController
{
    /**
     * Lists all Jour entities.
     *
     * @Route("/", name="jour", methods={"GET"})
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(Jour::class)->search(['order' => ['j.date_jour', 'DESC']]);

        return $this->render(
            'admin/jour/index.html.twig',
            [
                'entities' => $entities,
            ]
        );
    }

    /**
     * Creates a form to create a Jour entity.
     *
     * @param Jour $entity The entity
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createCreateForm(Jour $entity)
    {
        $form = $this->createForm(
            JourType::class,
            $entity,
            [
                'action' => $this->generateUrl('jour_new'),
                'method' => 'POST',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Create']);

        return $form;
    }

    /**
     * Displays a form to create a new Jour entity.
     *
     * @Route("/new", name="jour_new", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function new(Request $request)
    {
        $entity = new Jour();

        $prix_jour1 = $this->getParameter('mercredi_bundle_prix_jour1');
        $prix_jour2 = $this->getParameter('mercredi_bundle_prix_jour2');
        $prix_jour3 = $this->getParameter('mercredi_bundle_prix_jour3');

        $entity->setPrix1($prix_jour1);
        $entity->setPrix2($prix_jour2);
        $entity->setPrix3($prix_jour3);

        $form = $this->createCreateForm($entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            $entity->setUserAdd($user);

            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', 'La date a bien été ajoutée');

            return $this->redirectToRoute('jour');
        }

        return $this->render(
            'admin/jour/new.html.twig',
            [
                'entity' => $entity,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Jour entity.
     *
     * @Route("/{id}", name="jour_show", methods={"GET"})
     */
    public function show(Jour $jour)
    {
        $em = $this->getDoctrine()->getManager();

        $args = ['jour_id' => $jour->getId(), 'order' => 'enfant'];

        $presences = $em->getRepository(Presence::class)->search($args);

        $deleteForm = $this->createDeleteForm($jour->getId());

        return $this->render(
            'admin/jour/show.html.twig',
            [
                'entity' => $jour,
                'presences' => $presences,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to delete a Jour entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('jour_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Jour entity.
     *
     * @Route("/{id}/edit", name="jour_edit", methods={"GET","PUT"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function edit(Jour $jour, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createEditForm($jour);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            $this->addFlash('success', 'La date a bien été modifiée');

            return $this->redirectToRoute('jour');
        }

        return $this->render(
            'admin/jour/edit.html.twig',
            [
                'entity' => $jour,
                'edit_form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to edit a Jour entity.
     *
     * @param Jour $entity The entity
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createEditForm(Jour $entity)
    {
        $form = $this->createForm(
            JourType::class,
            $entity,
            [
                'action' => $this->generateUrl('jour_edit', ['id' => $entity->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Update']);

        return $form;
    }

    /**
     * Deletes a Jour entity.
     *
     * @Route("/{id}", name="jour_delete", methods={"DELETE"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function delete(Request $request, Jour $jour)
    {
        $form = $this->createDeleteForm($jour->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($jour);
            $em->flush();

            $this->addFlash('success', 'La date a bien été supprimée');
        }

        return $this->redirectToRoute('jour');
    }

    /**
     * Displays a form to edit an existing Animateur entity.
     *
     * @Route("/{id}/animateurs", name="jour_animateurs", methods={"GET","POST"})
     */
    public function animateurs(Request $request, Jour $jour)
    {
        $em = $this->getDoctrine()->getManager();
        $animateursOld = $jour->getAnimateurs()->toArray();

        $form = $this->createAnimateursForm($jour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $animateurs = $data->getAnimateurs()->toArray();

            if ($animateurs != $animateursOld) {
                $animateursenMoins = array_diff($animateursOld, $animateurs);
                $animateursenPlus = array_diff($animateurs, $animateursOld);

                if (count($animateursenPlus) > 0) {
                    foreach ($animateursenPlus as $animateur) {
                        $animateur->addJour($jour);
                    }
                }

                if (count($animateursenMoins) > 0) {
                    foreach ($animateursenMoins as $animateur) {
                        $animateur->removeJour($jour);
                    }
                }

                $em->flush();
                $this->addFlash('success', 'Les animateurs ont bien été affectés');
            } else {
                $this->addFlash('warning', 'Aucun changement effectué');
            }

            return $this->redirect($this->generateUrl('jour_show', ['id' => $jour->getId()]));
        }

        return $this->render(
            'admin/jour/animateurs.html.twig',
            [
                'entity' => $jour,
                'form' => $form->createView(),
            ]
        );
    }

    private function createAnimateursForm(Jour $entity)
    {
        $form = $this->createForm(
            JourAnimateursType::class,
            $entity,
            [
                'action' => $this->generateUrl('jour_animateurs', ['id' => $entity->getId()]),
                'method' => 'POST',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Update']);

        return $form;
    }
}
