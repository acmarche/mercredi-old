<?php

namespace AcMarche\Mercredi\Plaine\Controller;

use AcMarche\Mercredi\Commun\Utils\DateService;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Plaine\Entity\PlaineEnfant;
use AcMarche\Mercredi\Plaine\Form\PlaineEditType;
use AcMarche\Mercredi\Plaine\Form\PlaineType;
use AcMarche\Mercredi\Plaine\Form\Search\SearchPlaineType;
use AcMarche\Mercredi\Plaine\Manager\PlaineManager;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Plaine\Service\GroupeScolaireService;
use AcMarche\Mercredi\Plaine\Service\PlaineService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Plaine controller.
 *
 * @Route("/plaine")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class PlaineController extends AbstractController
{
    /**
     * @var PlaineRepository
     */
    private $plaineRepository;
    /**
     * @var DateService
     */
    private $dateService;
    /**
     * @var PlaineService
     */
    private $plaineService;
    /**
     * @var ScolaireService
     */
    private $scolaireService;
    /**
     * @var PlaineManager
     */
    private $plaineManager;
    /**
     * @var GroupeScolaireService
     */
    private $groupeScolaireService;

    public function __construct(
        PlaineManager $plaineManager,
        PlaineRepository $plaineRepository,
        DateService $dateService,
        ScolaireService $scolaireService,
        PlaineService $plaineService,
        GroupeScolaireService $groupeScolaireService
    ) {
        $this->plaineRepository = $plaineRepository;
        $this->dateService = $dateService;
        $this->plaineService = $plaineService;
        $this->scolaireService = $scolaireService;
        $this->plaineManager = $plaineManager;
        $this->groupeScolaireService = $groupeScolaireService;
    }

    /**
     * Lists all Plaine entities.
     *
     * @Route("/", name="plaine", methods={"GET"})
     */
    public function index(Request $request)
    {
        $session = $request->getSession();

        $data = [];

        if ($session->has('plaine_search')) {
            $data = unserialize($session->get('plaine_search'));
        }

        $search_form = $this->createForm(
            SearchPlaineType::class,
            $data,
            [
                'action' => $this->generateUrl('plaine'),
                'method' => 'GET',
            ]
        );

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
            if ($search_form->get('raz')->isClicked()) {
                $session->remove('plaine_search');
                $this->addFlash('success', 'La recherche a bien été réinitialisée.');

                return $this->redirectToRoute('plaine');
            }
        }

        $session->set('plaine_search', serialize($data));
        $plaines = $this->plaineRepository->search($data);

        return $this->render(
            'plaine/plaine/index.html.twig',
            [
                'form' => $search_form->createView(),
                'plaines' => $plaines,
            ]
        );
    }

    /**
     * Displays a form to create a new Plaine entity.
     *
     * @Route("/new", name="plaine_new", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function new(Request $request)
    {
        $plaine = $this->plaineManager->newInstance();

        $form = $form = $this->createForm(PlaineType::class, $plaine)
            ->add('submit', SubmitType::class, ['label' => 'Create']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $plaine->setUserAdd($user);

            //sinon date_jour par la reference de la plaine
            $this->plaineManager->setJours($plaine, $form->getData()->getJours());
            $this->plaineRepository->insert($plaine);

            $this->addFlash('success', 'La plaine a bien été ajoutée');

            return $this->redirectToRoute('plaine_show', ['slugname' => $plaine->getSlugname()]);
        }

        return $this->render(
            'plaine/plaine/new.html.twig',
            [
                'plaine' => $plaine,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Plaine entity.
     *
     * @Route("/{slugname}", name="plaine_show", methods={"GET"})
     */
    public function show(Plaine $plaine)
    {
        $em = $this->getDoctrine()->getManager();

        $groupes = $this->plaineService->getEnfantsByGroupeScolaire($plaine);

        $plaine_enfants = $em->getRepository(PlaineEnfant::class)->search(['plaine_id' => $plaine->getId()]);
        $maxs = $this->plaineService->getGroupesMax($plaine);

        $deleteForm = $this->createDeleteForm($plaine->getId());

        $firstDate = $this->plaineService->getFirstDatePlaine($plaine);

        $groupes2 = $this->groupeScolaireService->getEnfantsByGroupeScolaire($plaine_enfants, $firstDate);

        return $this->render(
            'plaine/plaine/show.html.twig',
            [
                'plaine' => $plaine,
                'groupes' => $groupes,
                'groupes2' => $groupes2,
                'maxs' => $maxs,
                'plaine_enfants' => $plaine_enfants,
                'plaineService' => $this->plaineService,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Plaine entity.
     *
     * @Route("/{slugname}/edit", name="plaine_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function edit(Plaine $plaine, Request $request)
    {
        if (0 == count($plaine->getMax())) {
            $this->plaineManager->initMax($plaine);
        }

        $form = $this->createForm(PlaineEditType::class, $plaine)
            ->add('Update', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->plaineRepository->save();

            $this->addFlash('success', 'La plaine a bien été modifiée');

            return $this->redirectToRoute('plaine_show', ['slugname' => $plaine->getSlugname()]);
        }

        return $this->render(
            'plaine/plaine/edit.html.twig',
            [
                'plaine' => $plaine,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Creates a form to delete a Plaine entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('plaine_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                ['label' => 'Supprimer la plaine', 'attr' => ['class' => 'btn-danger']]
            )
            ->getForm();
    }

    /**
     * Deletes a Plaine entity.
     *
     * @Route("/{id}", name="plaine_delete", methods={"DELETE"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function delete(Request $request, Plaine $plaine)
    {
        $form = $this->createDeleteForm($plaine->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->plaineRepository->remove($plaine);

            $this->addFlash('success', 'La plaine a bien été supprimée');
        }

        return $this->redirectToRoute('plaine');
    }
}
