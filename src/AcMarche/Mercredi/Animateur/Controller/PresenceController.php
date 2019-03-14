<?php

namespace AcMarche\Mercredi\Animateur\Controller;

use AcMarche\Mercredi\Admin\Repository\JourRepository;
use AcMarche\Mercredi\Admin\Repository\PresenceRepository;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use AcMarche\Mercredi\Commun\Utils\SortUtils;
use AcMarche\Mercredi\Plaine\Repository\PlaineJourRepository;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Presence controller.
 *
 * @Route("/presence")
 * @IsGranted("ROLE_MERCREDI_ANIMATEUR")
 */
class PresenceController extends AbstractController
{
    /**
     * @var PlaineJourRepository
     */
    private $plaineJourRepository;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var PlainePresenceRepository
     */
    private $plainePresenceRepository;
    /**
     * @var JourRepository
     */
    private $jourRepository;
    /**
     * @var SortUtils
     */
    private $sortUtils;
    /**
     * @var ScolaireService
     */
    private $scolaireService;

    public function __construct(
        JourRepository $jourRepository,
        PlaineJourRepository $plaineJourRepository,
        PresenceRepository $presenceRepository,
        PlainePresenceRepository $plainePresenceRepository,
        SortUtils $sortUtils,
        ScolaireService $scolaireService
    ) {
        $this->plaineJourRepository = $plaineJourRepository;
        $this->presenceRepository = $presenceRepository;
        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->jourRepository = $jourRepository;
        $this->sortUtils = $sortUtils;
        $this->scolaireService = $scolaireService;
    }

    /**
     * Lists all Presence entities.
     *
     * @Route("/", name="animateur_presence", methods={"GET"})
     *
     *
     */
    public function index(Request $request)
    {
        $date = new \DateTime();
        $date->modify('-1 week');

        $joursGarde = $this->jourRepository->getRecents($date);
        $joursPlaine = $this->plaineJourRepository->getRecents($date);
        $jours = array_merge($joursGarde, $joursPlaine);
        $jours = $this->sortUtils->sortJours($jours);

        return $this->render(
            'animateur/presence/index.html.twig',
            [
                'jours' => $jours,
            ]
        );
    }

    /**
     * Finds and displays a Presence entity.
     *
     * @Route("/{id}/{type}", name="animateur_presence_show", methods={"GET"})
     *
     */
    public function show(int $id, string $type)
    {
        $petits = $grands = $moyens = [];
        if ($type === 'plaine') {
            $jour = $this->plaineJourRepository->find($id);
            $presences = $this->plainePresenceRepository->findBy(['jour' => $jour]);
            foreach ($presences as $presence) {
                $presence->setEnfant($presence->getPlaineEnfant()->getEnfant());
            }
        } else {
            $jour = $this->jourRepository->find($id);
            $presences = $this->presenceRepository->findBy(['jour' => $jour]);
        }

        if (!$jour) {
            throw $this->createNotFoundException('Jour non trouvé');
        }

        $remarques = $jour->getRemarques();

        extract($this->scolaireService->groupPresences($presences, $type), EXTR_OVERWRITE);

        return $this->render(
            'animateur/presence/show.html.twig',
            array(
                'datejour' => $jour->getDateJour(),
                'petits' => $petits,
                'moyens' => $moyens,
                'grands' => $grands,
                'remarques' => $remarques,
                'display_remarques' => true,
                'presences' => $presences,
            )
        );
    }


}
