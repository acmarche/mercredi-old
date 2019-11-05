<?php

namespace AcMarche\Mercredi\Animateur\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Form\Search\SearchEnfantType;
use AcMarche\Mercredi\Admin\Repository\EnfantRepository;
use AcMarche\Mercredi\Admin\Repository\EnfantTuteurRepository;
use AcMarche\Mercredi\Admin\Repository\PresenceRepository;
use AcMarche\Mercredi\Admin\Service\FraterieService;
use AcMarche\Mercredi\Plaine\Repository\PlaineEnfantRepository;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/enfant")
 * @IsGranted("ROLE_MERCREDI_ANIMATEUR")
 */
class EnfantController extends AbstractController
{
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var PlainePresenceRepository
     */
    private $plainePresenceRepository;
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var FraterieService
     */
    private $fraterieService;
    /**
     * @var PlaineRepository
     */
    private $plaineRepository;
    /**
     * @var PlaineEnfantRepository
     */
    private $plaineEnfantRepository;
    /**
     * @var EnfantTuteurRepository
     */
    private $enfantTuteurRepository;

    public function __construct(
        PresenceRepository $presenceRepository,
        PlainePresenceRepository $plainePresenceRepository,
        EnfantRepository $enfantRepository,
        PlaineEnfantRepository $plaineEnfantRepository,
        EnfantTuteurRepository $enfantTuteurRepository,
        SessionInterface $session,
        FraterieService $fraterieService
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->enfantRepository = $enfantRepository;
        $this->session = $session;
        $this->fraterieService = $fraterieService;
        $this->plaineEnfantRepository = $plaineEnfantRepository;
        $this->enfantTuteurRepository = $enfantTuteurRepository;
    }

    /**
     * @Route("/", name="animateur_enfant")
     * @Route("/all/{all}", name="animateur_enfant_all")
     * @IsGranted("ROLE_MERCREDI_ANIMATEUR")
     */
    public function index(Request $request, $all = false)
    {
        $key = 'enfant_animateur_search';

        $data = [];
        $search = false;

        if ($this->session->has($key)) {
            $data = unserialize($this->session->get($key));
            $search = true;
        }

        $search_form = $this->createForm(
            SearchEnfantType::class,
            $data,
            [
                'method' => 'GET',
            ]
        );

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
            $search = true;
            if ($search_form->get('raz')->isClicked()) {
                $this->session->remove($key);
                $this->addFlash('success', 'La recherche a bien été réinitialisée.');

                return $this->redirectToRoute('animateur_enfant');
            }
            $this->session->set($key, serialize($data));
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
            'animateur/enfant/index.html.twig',
            [
                'form' => $search_form->createView(),
                'enfants' => $enfants,
                'search' => $search,
            ]
        );
    }

    /**
     * @Route("/{slugname}", name="animateur_enfant_show", methods={"GET"})
     */
    public function show(Enfant $enfant)
    {
        $allFratries = $this->fraterieService->getFratrie($enfant);
        $tuteurs = $this->enfantTuteurRepository->getTuteursByEnfant($enfant);

        $date = new \DateTime();
        $date->modify('-2 month');

        $presences = $this->presenceRepository->getByTuteurs($enfant, $tuteurs, $date);

        $plaines = $this->plaineEnfantRepository->search(['enfant_id' => $enfant->getId()]);

        return $this->render(
            'animateur/enfant/show.html.twig',
            [
                'enfant' => $enfant,
                'plaines' => $plaines,
                'fratries' => $allFratries,
                'tuteurs' => $tuteurs,
                'presences' => $presences,
            ]
        );
    }
}
