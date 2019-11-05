<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Events\EnfantEvent;
use AcMarche\Mercredi\Admin\Form\Enfant\EnfantType;
use AcMarche\Mercredi\Admin\Form\Search\SearchEnfantType;
use AcMarche\Mercredi\Admin\Repository\EnfantRepository;
use AcMarche\Mercredi\Admin\Service\Facture;
use AcMarche\Mercredi\Admin\Service\FileUploader;
use AcMarche\Mercredi\Admin\Service\FormService;
use AcMarche\Mercredi\Admin\Service\FraterieService;
use AcMarche\Mercredi\Plaine\Repository\PlaineEnfantRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Enfant controller.
 *
 * @Route("/enfant")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class EnfantController extends AbstractController
{
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var FileUploader
     */
    private $fileUploader;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var PlaineEnfantRepository
     */
    private $plaineEnfantRepository;
    /**
     * @var FormService
     */
    private $formService;
    /**
     * @var Facture
     */
    private $facture;
    /**
     * @var FraterieService
     */
    private $fraterieService;

    public function __construct(
        EnfantRepository $enfantRepository,
        PlaineEnfantRepository $plaineEnfantRepository,
        FileUploader $fileUploader,
        EventDispatcherInterface $eventDispatcher,
        FormService $formService,
        Facture $facture,
        FraterieService $fraterieService
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->fileUploader = $fileUploader;
        $this->eventDispatcher = $eventDispatcher;
        $this->plaineEnfantRepository = $plaineEnfantRepository;
        $this->formService = $formService;
        $this->facture = $facture;
        $this->fraterieService = $fraterieService;
    }

    /**
     * Lists all Enfant entities.
     *
     * @Route("/", name="enfant", methods={"GET"})
     * @Route("/all/{all}", name="enfant_all")
     */
    public function index(Request $request, $all = false)
    {
        $session = $request->getSession();
        $key = 'enfant_search';

        $data = [];
        $search = false;

        if ($session->has($key)) {
            $data = unserialize($session->get($key));
            $search = true;
        }

        $search_form = $this->createForm(
            SearchEnfantType::class,
            $data,
            [
                'action' => $this->generateUrl('enfant'),
                'method' => 'GET',
            ]
        );

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
            $search = true;
            if ($search_form->get('raz')->isClicked()) {
                $session->remove($key);
                $this->addFlash('success', 'La recherche a bien été réinitialisée.');

                return $this->redirectToRoute('enfant');
            }
            $session->set($key, serialize($data));
        }

        $enfants = [];
        if ($search) {
            $enfants = $this->enfantRepository->quickSearchActif($data);
        }
        if ($all) {
            $enfants = $this->enfantRepository->quickSearchActif([]);
            $search = true;
        }

        return $this->render(
            'admin/enfant/index.html.twig',
            [
                'search_form' => $search_form->createView(),
                'enfants' => $enfants,
                'search' => $search,
            ]
        );
    }

    /**
     * Displays a form to create a new Enfant entity.
     *
     * @Route("/new", name="enfant_new", methods={"GET","POST"})
     * @Route("/new/{id}", name="enfant_new_with_tuteur", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function new(Request $request, Tuteur $tuteur = null)
    {
        $enfant = new Enfant();
        $em = $this->getDoctrine()->getManager();

        if ($tuteur) {
            $enfant->setTuteur($tuteur);
        }

        $form = $this->createForm(EnfantType::class, $enfant)
            ->add('submit', SubmitType::class, ['label' => 'Create']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $enfant->setUserAdd($user);
            $tuteur = $enfant->getTuteur();

            if ($tuteur) {
                $enfant_tuteur = new EnfantTuteur();
                $enfant_tuteur->setTuteur($tuteur);
                $enfant_tuteur->setEnfant($enfant);
                $em->persist($enfant_tuteur);

                $enfant->addTuteur($enfant_tuteur);
            }

            $this->enfantRepository->insert($enfant);

            $this->fileUploader->traitementFiles($enfant);

            $this->enfantRepository->save();

            $this->addFlash('success', "L'enfant a bien été ajouté");

            return $this->redirectToRoute('enfant_show', ['slugname' => $enfant->getSlugname()]);
        }

        return $this->render(
            'admin/enfant/new.html.twig',
            [
                'enfant' => $enfant,
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Enfant entity.
     *
     * @Route("/{slugname}", name="enfant_show", methods={"GET"})
     */
    public function show(Enfant $enfant)
    {
        $this->eventDispatcher->dispatch(
            EnfantEvent::ENFANT_SHOW,
            new EnfantEvent($enfant)
        );

        $enfant = $this->enfantRepository->search(
            ['enfant' => $enfant, 'one' => true, 'archive' => 2]
        );

        $enfant_tuteur = new EnfantTuteur();
        $enfant_tuteur->setEnfant($enfant);

        $year = date('Y') + 1;
        $years = range(2014, $year);

        $allFratries = $this->fraterieService->getFratrie($enfant);
        $enfant_tuteurs = $this->facture->traitement($enfant);

        $plaines = $this->plaineEnfantRepository->search(['enfant_id' => $enfant->getId()]);

        $deleteForm = $this->createDeleteForm($enfant->getId());
        $form_detach = $this->formService->createDetachForm($enfant);
        $form_attach = $this->formService->createAttachForm($enfant_tuteur);
        $deletePresencesForm = $this->formService->createDeletePresencesForm($enfant);

        return $this->render(
            'admin/enfant/show.html.twig',
            [
                'enfant' => $enfant,
                'plaines' => $plaines,
                'fratries' => $allFratries,
                'years' => $years,
                'enfant_tuteurs' => $enfant_tuteurs,
                'delete_form' => $deleteForm->createView(),
                'form_detach' => $form_detach->createView(),
                'form_attach' => $form_attach->createView(),
                'form_delete_presences' => $deletePresencesForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Enfant entity.
     *
     * @Route("/{slugname}/edit", name="enfant_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function edit(Request $request, Enfant $enfant)
    {
        $form = $this->createForm(EnfantType::class, $enfant)
            ->add('submit', SubmitType::class, ['label' => 'Update']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newList = $form->get('accompagnateurs')->getData();
            $enfant->setAccompagnateurs($newList);
            $this->fileUploader->traitementFiles($enfant);

            $this->enfantRepository->save();

            $this->addFlash('success', "L'enfant a bien été mis à jour");

            return $this->redirectToRoute('enfant_show', ['slugname' => $enfant->getSlugname()]);
        }

        $this->eventDispatcher->dispatch(
            EnfantEvent::ENFANT_EDIT,
            new EnfantEvent($enfant)
        );

        return $this->render(
            'admin/enfant/edit.html.twig',
            [
                'enfant' => $enfant,
                'edit_form' => $form->createView(),
            ]
        );
    }

    /**
     * Creates a form to delete a Enfant entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('enfant_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }

    /**
     * Deletes a Enfant entity.
     *
     * @Route("/{id}", name="enfant_delete", methods={"DELETE"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function delete(Request $request, Enfant $enfant)
    {
        $form = $this->createDeleteForm($enfant->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->enfantRepository->remove($enfant);

            $this->addFlash('success', "L'enfant a bien été supprimé");
        }

        return $this->redirectToRoute('enfant');
    }
}
