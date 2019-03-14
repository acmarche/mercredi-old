<?php

namespace AcMarche\Mercredi\Ecole\Controller;

use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Repository\EcoleRepository;
use AcMarche\Mercredi\Admin\Repository\EnfantRepository;
use AcMarche\Mercredi\Admin\Repository\JourRepository;
use AcMarche\Mercredi\Admin\Repository\PresenceRepository;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use AcMarche\Mercredi\Ecole\Form\SearchEnfantForEcoleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Ecole controller.
 *
 *
 * @IsGranted("ROLE_MERCREDI_ECOLE")
 */
class IndexController extends AbstractController
{
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var EcoleRepository
     */
    private $ecoleRepository;
    /**
     * @var JourRepository
     */
    private $jourRepository;
    /**
     * @var ScolaireService
     */
    private $scolaireService;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;

    public function __construct(
        EcoleRepository $ecoleRepository,
        EnfantRepository $enfantRepository,
        JourRepository $jourRepository,
        ScolaireService $scolaireService,
        PresenceRepository $presenceRepository
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->ecoleRepository = $ecoleRepository;
        $this->jourRepository = $jourRepository;
        $this->scolaireService = $scolaireService;
        $this->presenceRepository = $presenceRepository;
    }

    /**
     * Lists all Ecole entities.
     *
     * @Route("/", name="home_ecole", methods={"GET","POST"})
     * @IsGranted("index_ecole")
     */
    public function index(Request $request)
    {
        $petits = $grands = $moyens = $presences = [];
        $dateJour = null;

        $search_form = $this->createForm(SearchEnfantForEcoleType::class);

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();

            $jour = $data['jour'];

            if ($jour instanceof Jour) {
                $dateJour = $jour->getDateJour();
            }

            $presences = $this->presenceRepository->searchForEcole($data);
            if (!$dateJour) {
                $presences = $this->cleanPresences($presences);
            }
        }

        extract($this->scolaireService->groupPresences($presences, 'mercredi'), EXTR_OVERWRITE);

        return $this->render(
            'ecole/index.html.twig',
            array(
                'form' => $search_form->createView(),
                'datejour' => $dateJour,
                'petits' => $petits,
                'moyens' => $moyens,
                'grands' => $grands,
                'presences' => $presences,
            )
        );
    }

    /**
     * Sinon me renvoie toutes les presences de l'enfant
     * @param Presence[] $presences
     * @return Presence[]
     */
    private function cleanPresences(iterable $presences)
    {
        $data = [];
        foreach ($presences as $presence) {
            $enfant = $presence->getEnfant();
            $data[$enfant->getId()] = $presence;
        }

        return $data;

    }
}
