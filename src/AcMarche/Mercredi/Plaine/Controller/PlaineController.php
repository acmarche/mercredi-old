<?php

namespace AcMarche\Mercredi\Plaine\Controller;

use AcMarche\Mercredi\Plaine\Manager\PlaineManager;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Plaine\Service\PlaineService;
use AcMarche\Mercredi\Plaine\Entity\PlaineEnfant;
use AcMarche\Mercredi\Commun\Utils\DateService;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Plaine\Form\PlaineEditType;
use AcMarche\Mercredi\Plaine\Form\PlaineType;
use AcMarche\Mercredi\Plaine\Form\Search\SearchPlaineType;

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

    public function __construct(
        PlaineManager $plaineManager,
        PlaineRepository $plaineRepository,
        DateService $dateService,
        ScolaireService $scolaireService,
        PlaineService $plaineService
    ) {
        $this->plaineRepository = $plaineRepository;
        $this->dateService = $dateService;
        $this->plaineService = $plaineService;
        $this->scolaireService = $scolaireService;
        $this->plaineManager = $plaineManager;
    }

    /**
     * Lists all Plaine entities.
     *
     * @Route("/", name="plaine", methods={"GET"})
     *
     */
    public function index(Request $request)
    {
        $session = $request->getSession();

        $data = array();

        if ($session->has("plaine_search")) {
            $data = unserialize($session->get("plaine_search"));
        }

        $search_form = $this->createForm(
            SearchPlaineType::class,
            $data,
            array(
                'action' => $this->generateUrl('plaine'),
                'method' => 'GET',
            )
        );

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
            if ($search_form->get('raz')->isClicked()) {
                $session->remove("plaine_search");
                $this->addFlash('success', 'La recherche a bien été réinitialisée.');

                return $this->redirectToRoute('plaine');
            }
        }

        $session->set('plaine_search', serialize($data));
        $plaines = $this->plaineRepository->search($data);

        return $this->render(
            'plaine/plaine/index.html.twig',
            array(
                'form' => $search_form->createView(),
                'plaines' => $plaines,
            )
        );
    }

    /**
     * Displays a form to create a new Plaine entity.
     *
     * @Route("/new", name="plaine_new", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     *
     */
    public function new(Request $request)
    {
        $plaine = $this->plaineManager->newInstance();

        $form = $form = $this->createForm(PlaineType::class, $plaine)
            ->add('submit', SubmitType::class, array('label' => 'Create'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $plaine->setUserAdd($user);

            //sinon date_jour par la reference de la plaine
            $this->plaineManager->setJours($plaine, $form->getData()->getJours());
            $this->plaineRepository->insert($plaine);

            $this->addFlash('success', "La plaine a bien été ajoutée");

            return $this->redirectToRoute('plaine_show', array('slugname' => $plaine->getSlugname()));
        }

        return $this->render(
            'plaine/plaine/new.html.twig',
            array(
                'plaine' => $plaine,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Plaine entity.
     *
     * @Route("/{slugname}", name="plaine_show", methods={"GET"})
     *
     */
    public function show(Plaine $plaine)
    {
        $em = $this->getDoctrine()->getManager();

        $groupes = $this->plaineService->getEnfantsByGroupeScolaire($plaine);

        $plaine_enfants = $em->getRepository(PlaineEnfant::class)->search(array('plaine_id' => $plaine->getId()));
        $maxs = $this->plaineService->getGroupesMax($plaine);

        $deleteForm = $this->createDeleteForm($plaine->getId());

        return $this->render(
            'plaine/plaine/show.html.twig',
            array(
                'plaine' => $plaine,
                'groupes' => $groupes,
                'maxs' => $maxs,
                'plaine_enfants' => $plaine_enfants,
                'plaineService' => $this->plaineService,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Plaine entity.
     *
     * @Route("/{slugname}/edit", name="plaine_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     *
     */
    public function edit(Plaine $plaine, Request $request)
    {
        if (count($plaine->getMax()) == 0) {
            $this->plaineManager->initMax($plaine);
        }

        $form = $this->createForm(PlaineEditType::class, $plaine)
            ->add('Update', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->plaineRepository->save();

            $this->addFlash('success', "La plaine a bien été modifiée");

            return $this->redirectToRoute('plaine_show', array('slugname' => $plaine->getSlugname()));
        }

        return $this->render(
            'plaine/plaine/edit.html.twig',
            array(
                'plaine' => $plaine,
                'form' => $form->createView(),
            )
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
            ->setAction($this->generateUrl('plaine_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                array('label' => 'Supprimer la plaine', 'attr' => array('class' => 'btn-danger'))
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

            $this->addFlash('success', "La plaine a bien été supprimée");
        }

        return $this->redirectToRoute('plaine');
    }

}
