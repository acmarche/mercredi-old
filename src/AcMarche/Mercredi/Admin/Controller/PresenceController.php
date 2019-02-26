<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Admin\Form\Presence\PresenceEditType;
use AcMarche\Mercredi\Admin\Form\Presence\PresenceType;
use AcMarche\Mercredi\Admin\Service\EnfantUtils;
use AcMarche\Mercredi\Admin\Service\Facture;
use AcMarche\Mercredi\Admin\Service\PresenceService;
use AcMarche\Mercredi\Plaine\Entity\PlaineJour;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Form\Search\SearchPresenceByMonthType;
use AcMarche\Mercredi\Admin\Form\Search\SearchPresenceType;


/**
 * Presence controller.
 *
 * @Route("/presence")
 * @IsGranted("ROLE_MERCREDI_READ")
 *
 */
class PresenceController extends AbstractController
{
    private const KEY_SESSION = 'message_emails_from_groupe';

    /**
     * @var ScolaireService
     */
    private $scolaireService;
    /**
     * @var PresenceService
     */
    private $presenceService;
    /**
     * @var Facture
     */
    private $facture;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(
        ScolaireService $scolaireService,
        PresenceService $presenceService,
        Facture $facture,
        SessionInterface $session
    ) {
        $this->scolaireService = $scolaireService;
        $this->presenceService = $presenceService;
        $this->facture = $facture;
        $this->session = $session;
    }

    /**
     *
     * @Route("/", name="presence", methods={"GET"})
     *
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $key = "presences_listing";
        $args = $presences = $petits = $grands = $moyens = [];
        $dateJour = $display_remarque = $type = $jour_object = false;
        $remarques = '';

        $search_form = $this->createForm(
            SearchPresenceType::class,
            array('remarques' => true),
            array(
                'method' => 'GET',
            )
        );

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();

            $jour = $data['jour']; //jour au format idjour_plaine
            list($jour_id, $type) = explode("_", $jour);

            $display_remarque = $data['remarques'];
            $ecole = $data['ecole'];

            $args['jour_id'] = $jour_id;
            $args['ecole'] = $ecole;
            $args['one'] = true;

            $session->set($key, serialize($args));

            if ($type == 'mercredi') {
                $jour_object = $em->getRepository(Jour::class)->search($args);
            } elseif ($type == 'plaine') {
                $jour_object = $em->getRepository(PlaineJour::class)->search($args);
            } else {
                $jour_object = false;
            }

            if (!$jour_object instanceof Jour and !$jour_object instanceof PlaineJour) {
                throw $this->createNotFoundException('Unable to find Jour entity.');
            }

            unset($args['one']);

            $dateJour = $jour_object->getDateJour(); //for title page
            //je ne prend PAS les enfants mis absent
            $args['absent'] = 1;

            if ($type == 'mercredi') {
                $remarques = $jour_object->getRemarques();
                $args['order'] = 'enfant';
                $presences = $em->getRepository(Presence::class)->search($args);
            }

            if ($type == 'plaine') {
                $plaine = $jour_object->getPlaine();
                $remarques = $plaine->getRemarques();
                $presences = $em->getRepository(PlainePresence::class)->search($args);
            }
        }

        extract($this->scolaireService->groupPresences($presences, $type), EXTR_OVERWRITE);
        $args['type'] = $type;
        $this->session->set(self::KEY_SESSION, $args);

        return $this->render(
            'admin/presence/index.html.twig',
            array(
                'datejour' => $dateJour,
                'jour' => $jour_object,
                'search_form' => $search_form->createView(),
                'petits' => $petits,
                'moyens' => $moyens,
                'grands' => $grands,
                'remarques' => $remarques,
                'display_remarques' => $display_remarque,
            )
        );
    }

    /**
     * Displays a form to create a new Presence entity.
     *
     * @Route("/new/{id}", name="presence_new", methods={"GET","POST"})
     * @IsGranted("add_presence", subject="enfant")
     *
     */
    public function new(Request $request, Enfant $enfant)
    {
        $tuteurs = EnfantUtils::hasParents($enfant);

        if (count($tuteurs) == 0) {
            $this->addFlash('danger', "L'enfant n'a pas de parent !");

            return $this->redirectToRoute('enfant_show', array('slugname' => $enfant->getSlugname()));
        }

        $user = $this->getUser();
        $presence = $this->presenceService->initPresence($enfant, $user);

        if (count($tuteurs) == 1) {
            $presence->setTuteur($tuteurs[0]);
        }

        $form = $form = $this->createForm(PresenceType::class, $presence)
            ->add('submit', SubmitType::class, array('label' => 'Create'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $jours = $data->getJours();
            $tuteur = $data->getTuteur();
            $this->presenceService->addPresences($presence, $tuteur, $jours);

            $this->addFlash('success', "La présence a bien été ajoutée");

            return $this->redirectToRoute('enfant_show', array('slugname' => $enfant->getSlugname()));
        }

        return $this->render(
            'admin/presence/new.html.twig',
            array(
                'entity' => $presence,
                'enfant' => $enfant,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Presence entity.
     *
     * @Route("/{id}", name="presence_show", methods={"GET"})
     * @IsGranted("show", subject="presence")
     *
     */
    public function show(Presence $presence)
    {
        $this->facture->handlePresence($presence);
        $deleteForm = $this->createDeleteForm($presence->getId());

        return $this->render(
            'admin/presence/show.html.twig',
            array(
                'entity' => $presence,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Presence entity.
     *
     * @Route("/{id}/edit", name="presence_edit", methods={"GET","POST"})
     * @IsGranted("edit", subject="presence")
     *
     */
    public function edit(Presence $presence, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $form = $this->createForm(PresenceEditType::class, $presence)
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            $this->addFlash('success', "La présence a bien été modifiée");

            return $this->redirectToRoute('presence_show', array('id' => $presence->getId()));
        }

        return $this->render(
            'admin/presence/edit.html.twig',
            array(
                'entity' => $presence,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * Creates a form to delete a Presence entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('presence_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }

    /**
     * Deletes a Presence entity.
     *
     * @Route("/{id}", name="presence_delete", methods={"DELETE"})
     * @IsGranted("delete", subject="presence")
     */
    public function delete(Request $request, Presence $presence)
    {
        $form = $this->createDeleteForm($presence->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $enfant = $presence->getEnfant();

            $em->remove($presence);
            $em->flush();

            $this->addFlash('success', "La présence a bien été effacée");
        }

        return $this->redirectToRoute('enfant_show', array('slugname' => $enfant->getSlugname()));
    }

    /**
     * Deletes a Presence entity.
     *
     *
     * @Route("/delete/multiple/{id}", name="presences_delete", methods={"DELETE"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function deleteMultiple(Request $request, Enfant $enfant)
    {
        $ids = $request->get('presences', array());
        if (count($ids) == 0) {
            $this->addFlash('danger', 'Aucune présence sélectionnée');
        } else {
            $em = $this->getDoctrine()->getManager();
            $presences = $em->getRepository(Presence::class)->findBy(['id' => $ids]);
            foreach ($presences as $presence) {
                $em->remove($presence);
                $em->flush();
            }

            $this->addFlash('success', "La présence a bien été effacée");
        }

        return $this->redirectToRoute('enfant_show', array('slugname' => $enfant->getSlugname()));
    }

    /**
     * Liste toutes les presences par mois
     *
     * @Route("/by/mois", name="presence_mois", methods={"GET"})
     * @IsGranted("ROLE_MERCREDI_READ")
     */
    public function indexByMonth(Request $request)
    {
        $mois = $type = false;
        $allenfants = $allpresences = array();

        $search_form = $this->createForm(
            SearchPresenceByMonthType::class,
            array(),
            array(
                'action' => $this->generateUrl('presence_mois'),
                'method' => 'GET',
            )
        );

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
            $mois = $data['mois'];
            $type = $data['quoi'];

            if (!\DateTime::createFromFormat('d/m/Y', "01/".$mois)) {
                $this->addFlash('danger', "Mauvais format de date");

                return $this->redirectToRoute('presence_mois');
            }

            $result = $this->presenceService->getPresencesAndEnfantsByMonth($mois, $type);
            $allpresences = $result['allpresences'];
            $allenfants = $result['allenfants'];
        }

        return $this->render(
            'admin/presence/month.html.twig',
            array(
                'mois' => $mois,
                'type' => $type,
                'enfants' => $allenfants,
                'presences' => $allpresences,
                'search_form' => $search_form->createView(),
            )
        );
    }
}
