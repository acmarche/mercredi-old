<?php

namespace AcMarche\Mercredi\Plaine\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Entity\Paiement;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Plaine\Entity\PlaineEnfant;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use AcMarche\Mercredi\Plaine\Events\PlaineEvent;
use AcMarche\Mercredi\Plaine\Form\PlainePresenceEditType;
use AcMarche\Mercredi\Plaine\Form\PlainePresenceJoursType;
use AcMarche\Mercredi\Plaine\Form\PlainePresencePaiementType;
use AcMarche\Mercredi\Plaine\Form\PlainePresenceTuteurType;
use AcMarche\Mercredi\Plaine\Form\PlainePresenceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * PlainePresence controller.
 *
 * @Route("/plainepresence")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class PlainePresenceController extends AbstractController
{
    /**
     * Creates a new PlainePresence entity.
     *
     * @Route("/", name="plainepresence_create", methods={"POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function create(Request $request)
    {
        $entity = new PlainePresence();
        $em = $this->getDoctrine()->getManager();

        $data = $request->get('plaine_presence', []);
        $plaine_id = isset($data['plaine']) ? $data['plaine'] : 0;
        $enfant_id = isset($data['enfant']) ? $data['enfant'] : 0;

        $plaine = $em->getRepository(Plaine::class)->find($plaine_id);

        if (!$plaine) {
            throw $this->createNotFoundException('Unable to find Plaine entity.');
        }

        $enfant = $em->getRepository(Enfant::class)->find($enfant_id);

        if (!$plaine) {
            throw $this->createNotFoundException('Unable to find Plaine entity.');
        }

        /**
         * enfant deja inscrit ?
         */
        $args = ['enfant_id' => $enfant->getId(), 'plaine_id' => $plaine->getId()];
        $enfant_check = $em->getRepository(PlaineEnfant::class)->search($args);

        if (count($enfant_check) > 0) {
            $this->addFlash('danger', 'Un enfant ne peut être inscrit deux fois à la même plaine.');

            return $this->redirectToRoute('plaine_show', ['slugname' => $plaine->getSlugname()]);
        }

        $entity->setPlaine($plaine);
        $tuteur = false;

        if ($enfant) {
            $entity->setEnfant($enfant);
            $enfant_tuteur = $enfant->getTuteurs();
            if (1 == count($enfant_tuteur)) {
                $tuteur = $enfant_tuteur[0]->getTuteur();
                $entity->setTuteur($tuteur);
            }
        }

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();

            $plaineEnfant = new PlaineEnfant();
            $plaineEnfant->setPlaine($plaine);
            $plaineEnfant->setEnfant($enfant);

            $data = $form->getData();
            $jours = $data->getJours();

            foreach ($jours as $jour) {
                $presence = clone $entity;
                $presence->setJour($jour);
                $presence->setPlaineEnfant($plaineEnfant);
                $presence->setUserAdd($user);

                $em->persist($presence);
            }

            $em->flush();

            $this->addFlash('success', "L' enfant a bien été ajouté");

            return $this->redirectToRoute(
                'plainepresence_show_enfant',
                ['plaine_slugname' => $plaine->getSlugname(), 'enfant_slugname' => $enfant->getSlugname()]
            );
        }

        return $this->render(
            'plaine/plaine_presence/new.html.twig',
            [
                'entity' => $entity,
                'plaine' => $plaine,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Creates a form to create a PlainePresence entity.
     *
     * @param PlainePresence $entity The entity
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createCreateForm(PlainePresence $entity)
    {
        $form = $this->createForm(
            PlainePresenceType::class,
            $entity,
            [
                'action' => $this->generateUrl('plainepresence_create'),
                'method' => 'POST',
                'plaine_presence' => $entity,
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Create']);

        return $form;
    }

    /**
     * Ajout d'un enfant a une plaine.
     *
     * @Route("/new/{slugname}", name="plainepresence_new", methods={"GET"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function new(Plaine $plaine)
    {
        if (count($plaine->getJours()) < 1) {
            $this->addFlash('danger', 'Cette plaine ne comporte aucune date !');

            return $this->redirectToRoute('plaine_show', ['slugname' => $plaine->getSlugname()]);
        }

        $entity = new PlainePresence();
        $entity->setPlaine($plaine);

        $form = $this->createCreateForm($entity);

        return $this->render(
            'plaine/plaine_presence/new.html.twig',
            [
                'entity' => $entity,
                'plaine' => $plaine,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Creates a form to delete a PlainePresence entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('plainepresence_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }

    /**
     * Permet d'ajouter des jours de la plaine.
     *
     * @Route("/edit/{id}", name="plainepresence_edit", methods={"GET","PUT"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function edit(PlainePresence $plainePresence, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $plaine_enfant = $plainePresence->getPlaineenfant();
        $enfant = $plaine_enfant->getEnfant();
        $plaine = $plaine_enfant->getPlaine();
        $jour = $plainePresence->getJour();

        $enfantTuteurs = $em->getRepository(EnfantTuteur::class)->findBy(['enfant' => $enfant]);

        if (0 == count($enfantTuteurs)) {
            $this->addFlash('danger', "L'enfant n'a aucun tuteur");

            return $this->redirectToRoute(
                'plainepresence_show_enfant',
                [
                    'enfant_slugname' => $enfant->getSlugname(),
                    'plaine_slugname' => $plaine->getSlugname(),
                ]
            );
        }

        $editForm = $this->createEditForm($plainePresence, $enfant);

        /**
         * Pour si tuteur change et que deja paiement de mis.
         */
        $paiment = $plainePresence->getPaiement();
        $tuteur = false;

        if ($paiment) {
            $tuteur = $plainePresence->getTuteur();
        }

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if ($tuteur) {
                $data = $editForm->getData();
                $tuteur_new = $data->getTuteur();

                if ($tuteur_new) {
                    if ($tuteur->getId() != $tuteur_new->getId()) {
                        $plainePresence->setPaiement(null);
                    }
                }
            }

            $em->flush();
            $this->addFlash('success', 'Les présences ont bien été modifiées');

            return $this->redirectToRoute(
                'plainepresence_show_enfant',
                [
                    'enfant_slugname' => $enfant->getSlugname(),
                    'plaine_slugname' => $plaine->getSlugname(),
                ]
            );
        }

        return $this->render(
            'plaine/plaine_presence/edit.html.twig',
            [
                'enfant' => $enfant,
                'jour' => $jour,
                'plaine' => $plaine,
                'edit_form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to edit a PlainePresence entity.
     *
     * @param PlainePresence $entity The entity
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createEditForm(PlainePresence $plaine_presence, Enfant $enfant)
    {
        $form = $this->createForm(
            PlainePresenceEditType::class,
            $plaine_presence,
            [
                'action' => $this->generateUrl(
                    'plainepresence_edit',
                    [
                        'id' => $plaine_presence->getId(),
                    ]
                ),
                'method' => 'PUT',
                'enfant' => $enfant,
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Update']);

        return $form;
    }

    /**
     * Permet d'ajouter des jours de la plaine.
     *
     * @Route("/jours/{plaine_slugname}/{enfant_slugname}", name="plainepresence_jours", methods={"GET","PUT"})
     * @ParamConverter("plaine", class="AcMarche\Mercredi\Plaine\Entity\Plaine", options={"mapping": {"plaine_slugname": "slugname"}})
     * @ParamConverter("enfant", class="AcMarche\Mercredi\Admin\Entity\Enfant", options={"mapping": {"enfant_slugname": "slugname"}})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function jours(Request $request, Plaine $plaine, Enfant $enfant)
    {
        $em = $this->getDoctrine()->getManager();

        $plaine_presence = new PlainePresence();
        $plaine_presence->setEnfant($enfant);
        $plaine_presence->setPlaine($plaine);

        $enfant_id = $enfant->getId();
        $plaine_id = $plaine->getId();
        $args = ['enfant' => $enfant_id, 'plaine' => $plaine_id];

        $plaineEnfant = $em->getRepository(PlaineEnfant::class)->findOneBy($args);
        //jours pour lesquels enfant est inscrit
        $jours_enfant = $em->getRepository(PlainePresence::class)->getJoursInscrits($plaineEnfant);

        $plaine_presence->setPlaineEnfant($plaineEnfant);
        //coche les jours dja inscrits
        $plaine_presence->setJours($jours_enfant);

        $jours_plaine = $plaine->getJours();

        $editForm = $this->createJoursForm($plaine, $enfant, $jours_plaine, $plaine_presence);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $data = $editForm->getData();
            $jours_new = $data->getJours();
            $user = $this->getUser();

            /**
             * si que un tuteur pour l'enfant je l'ajoute d'office.
             */
            $enfant = $plaineEnfant->getEnfant();
            if ($enfant) {
                $enfant_tuteur = $enfant->getTuteurs();
                if (1 == count($enfant_tuteur)) {
                    $tuteur = $enfant_tuteur[0]->getTuteur();
                    $plaine_presence->setTuteur($tuteur);
                }
            }

            foreach ($jours_new as $jour) {
                if (!in_array($jour, $jours_enfant)) { //je n'inscrits pas jour ou dja inscrit
                    $presence = clone $plaine_presence;
                    $presence->setJour($jour);
                    $presence->setUserAdd($user);
                    $em->persist($presence);
                }
            }

            /**
             * je supprime les presences qui n'ont pas ete recochees.
             */
            $jours_to_delete = [];
            foreach ($jours_enfant as $jour) {
                if (!in_array($jour, $jours_new)) {
                    $jour_id = $jour->getId();

                    $args = ['enfant' => $enfant_id, 'plaine' => $plaine_id];
                    $plaine_enfant = $em->getRepository(PlaineEnfant::class)->findOneBy($args);

                    if ($plaine_enfant) {
                        $args2 = ['plaine_enfant_id' => $plaine_enfant->getId(), 'jour_id' => $jour_id];
                        $presences_to_delete = $em->getRepository(PlainePresence::class)->search($args2);

                        if (is_array($presences_to_delete) and 1 == count($presences_to_delete)) {
                            $presence_to_delete = $presences_to_delete[0];
                            $em->remove($presence_to_delete);
                        }
                    }
                }
            }

            $em->flush();
            $this->addFlash('success', 'Les présences ont bien été modifiées');

            return $this->redirectToRoute(
                'plainepresence_show_enfant',
                [
                    'enfant_slugname' => $enfant->getSlugname(),
                    'plaine_slugname' => $plaine->getSlugname(),
                ]
            );
        }

        return $this->render(
            'plaine/plaine_presence/jours.html.twig',
            [
                'enfant' => $enfant,
                'plaine' => $plaine,
                'edit_form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to edit a PlainePresence entity.
     *
     * @param PlainePresence $entity The entity
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createJoursForm(Plaine $plaine, Enfant $enfant, $jours_plaine, $plaine_presence)
    {
        $form = $this->createForm(
            PlainePresenceJoursType::class,
            $plaine_presence,
            [
                'action' => $this->generateUrl(
                    'plainepresence_jours',
                    [
                        'plaine_slugname' => $plaine->getSlugname(),
                        'enfant_slugname' => $enfant->getSlugname(),
                    ]
                ),
                'method' => 'PUT',
                'jours_plaine' => $jours_plaine,
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Update']);

        return $form;
    }

    /**
     * Deletes a PlainePresence entity.
     *
     * @Route("/{id}", name="plainepresence_delete", methods={"DELETE"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function delete(Request $request, PlainePresence $plainePresence)
    {
        $form = $this->createDeleteForm($plainePresence->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $plaine = $plainePresence->getPlaine();
            $enfant = $plainePresence->getEnfant();

            $em->remove($plainePresence);
            $em->flush();

            $this->addFlash('success', 'La présence a bien été supprimée');

            return $this->redirectToRoute(
                'plainepresence_show_enfant',
                [
                    'plaine_slugname' => $plaine->getSlugname(),
                    'enfant_slugname' => $enfant->getSlugname(),
                ]
            );
        }

        return $this->redirectToRoute('plaine');
    }

    /**
     * Set a tuteur sur une date.
     *
     * @Route("/tuteur/{plaine_slugname}/{enfant_slugname}", name="plainepresence_tuteur")
     * @ParamConverter("plaine", class="AcMarche\Mercredi\Plaine\Entity\Plaine", options={"mapping": {"plaine_slugname": "slugname"}})
     * @ParamConverter("enfant", class="AcMarche\Mercredi\Admin\Entity\Enfant", options={"mapping": {"enfant_slugname": "slugname"}})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function tuteur(Request $request, Plaine $plaine, Enfant $enfant)
    {
        $em = $this->getDoctrine()->getManager();

        $plaine_presence = new PlainePresence();
        $plaine_presence->setEnfant($enfant);
        $plaine_presence->setPlaine($plaine);

        $enfant_id = $enfant->getId();
        $plaine_id = $plaine->getId();
        $args = ['enfant' => $enfant_id, 'plaine' => $plaine_id];

        $plaineEnfant = $em->getRepository(PlaineEnfant::class)->findOneBy($args);
        //jours pour lesquels enfant est inscrit
        $jours_enfant = $em->getRepository(PlainePresence::class)->getJoursInscrits($plaineEnfant);

        $plaine_presence->setPlaineEnfant($plaineEnfant);
        //coche les jours dja inscrits
        $plaine_presence->addJours($jours_enfant);

        $enfant_tuteurs = $enfant->getTuteurs();

        $tuteurs = [];
        foreach ($enfant_tuteurs as $enfant_tuteur) {
            $tuteur = $enfant_tuteur->getTuteur();
            $tuteurs[] = $tuteur;
        }

        $plaine_presence->addTuteurs($tuteurs);

        $form = $this->createForm(
            PlainePresenceTuteurType::class,
            $plaine_presence,
            [
                'action' => $this->generateUrl(
                    'plainepresence_tuteur',
                    [
                        'enfant_slugname' => $enfant->getSlugname(),
                        'plaine_slugname' => $plaine->getSlugname(),
                    ]
                ),
                'plaine_presence' => $plaine_presence,
                'method' => 'POST',
            ]
        );

        $form->add(
            'submit',
            SubmitType::class,
            ['label' => 'Attribuer le tuteur', 'attr' => ['class' => 'btn-primary']]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $plaineEnfant = $data->getPlaineEnfant();

            $enfant_id_verif = $plaineEnfant->getEnfant()->getId();
            $plaine_id_verif = $plaineEnfant->getPlaine()->getId();

            if (($enfant_id != $enfant_id_verif) or ($plaine_id != $plaine_id_verif)) {
                throw $this->createNotFoundException('Unable to find presence entity.');
            }

            $plaine_enfant_id = $plaineEnfant->getId();
            $jours = $data->getJours();

            $tuteur = $data->getTuteur();
            $args = ['plaine_enfant' => $plaine_enfant_id];

            if ($tuteur) {
                foreach ($jours as $jour) {
                    $jour_id = $jour->getId();
                    $args['jour'] = $jour_id;
                    $presence = $em->getRepository(PlainePresence::class)->findOneBy($args);
                    $presence->setTuteur($tuteur);
                    $em->persist($presence);
                }
            }

            $em->flush();

            $this->addFlash('success', 'Le tuteur a bien été associé');

            return $this->redirectToRoute(
                'plainepresence_show_enfant',
                [
                    'plaine_slugname' => $plaine->getSlugname(),
                    'enfant_slugname' => $enfant->getSlugname(),
                ]
            );
        }

        return $this->render(
            'plaine/plaine_presence/tuteur.html.twig',
            [
                'form' => $form->createView(),
                'enfant' => $enfant,
                'plaine' => $plaine,
            ]
        );
    }

    /**
     * Associe un paiement a une presence de la plaine.
     *
     * @Route("/paiement/{id}/{paiementid}", name="plainepresence_paiement")
     * @ParamConverter("paiement", class="AcMarche\Mercredi\Admin\Entity\Paiement", options={"mapping": {"paiementid": "id"}})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function paiement(
        Request $request,
        PlainePresence $plainePresence,
        Paiement $paiement,
        EventDispatcherInterface $eventDispatcher
    ) {
        $em = $this->getDoctrine()->getManager();
        $tuteur = $plainePresence->getTuteur();
        $plaine_enfant = $plainePresence->getPlaineEnfant();
        $plaine = $plaine_enfant->getPlaine();

        $enfant = $plaine_enfant->getEnfant();

        if (!$tuteur) {
            $this->addFlash('danger', 'Pour payer un tuteur doit être attribué');

            return $this->redirectToRoute(
                'plainepresence_show_enfant',
                [
                    'plaine_slugname' => $plaine->getSlugname(),
                    'enfant_slugname' => $enfant->getSlugname(),
                ]
            );
        }

        $presencesOld = clone $paiement->getPlainePresences();

        $form = $this->createForm(
            PlainePresencePaiementType::class,
            $paiement,
            [
                'action' => $this->generateUrl(
                    'plainepresence_paiement',
                    [
                        'id' => $plainePresence->getId(),
                        'paiementid' => $paiement->getId(),
                    ]
                ),
                'method' => 'POST',
                'plaine_enfant' => $plaine_enfant,
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $presences = $form->getData()->getPlainePresences();
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
                    $tuteurPresence = $presence->getTuteur();

                    if (!$tuteurPresence) {
                        $presence->setTuteur($tuteur);
                    }

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

            $event = new  PlaineEvent($plaine, $plainePresence);
            $eventDispatcher->dispatch($event, PlaineEvent::PLAINE_PAIEMENT);

            return $this->redirectToRoute(
                'plainepresence_show_enfant',
                [
                    'plaine_slugname' => $plaine->getSlugname(),
                    'enfant_slugname' => $enfant->getSlugname(),
                ]
            );
        }

        return $this->render(
            'plaine/plaine_presence/paiement.html.twig',
            [
                'form' => $form->createView(),
                'enfant' => $enfant,
                'plaine' => $plaine,
            ]
        );
    }
}
