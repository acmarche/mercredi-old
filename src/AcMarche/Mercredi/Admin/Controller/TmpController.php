<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Repository\EnfantRepository;
use AcMarche\Mercredi\Admin\Repository\EnfantTuteurRepository;
use AcMarche\Mercredi\Admin\Repository\TuteurRepository;
use AcMarche\Mercredi\Admin\Service\FacturePlaine;
use AcMarche\Mercredi\Commun\Utils\SortUtils;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Plaine\Repository\PlaineEnfantRepository;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Plaine\Service\PlaineService;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TmpController extends AbstractController
{
    /**
     * @var PlaineRepository
     */
    private $plaineRepository;
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var PlainePresenceRepository
     */
    private $plainePresenceRepository;
    /**
     * @var PlaineEnfantRepository
     */
    private $plaineEnfantRepository;
    /**
     * @var EnfantTuteurRepository
     */
    private $enfantTuteurRepository;
    /**
     * @var FacturePlaine
     */
    private $facturePlaine;
    /**
     * @var PlaineService
     */
    private $plaineService;

    public function __construct(
        PlaineRepository $plaineRepository,
        PlainePresenceRepository $plainePresenceRepository,
        PlaineEnfantRepository $plaineEnfantRepository,
        TuteurRepository $tuteurRepository,
        EnfantRepository $enfantRepository,
        EnfantTuteurRepository $enfantTuteurRepository,
        FacturePlaine $facturePlaine,
        PlaineService $plaineService
    ) {
        $this->plaineRepository = $plaineRepository;
        $this->tuteurRepository = $tuteurRepository;
        $this->enfantRepository = $enfantRepository;
        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->plaineEnfantRepository = $plaineEnfantRepository;
        $this->enfantTuteurRepository = $enfantTuteurRepository;
        $this->facturePlaine = $facturePlaine;
        $this->plaineService = $plaineService;
    }

    /**
     * @Route("/covid", name="plaine_tmp")
     * @IsGranted("ROLE_MERCREDI_READ")
     */
    public function index()
    {
        $plaine = $this->plaineRepository->find(27);
        $tuteurs = $this->getTuteurs($plaine);

        $data = [];
        $coutTotal = 0;

        foreach ($tuteurs as $tuteur) {
            if (48 != $tuteur->getId()) {

            }
            $coutParTuteur = 0;
            $data[$tuteur->getId()]['tuteur'] = $tuteur;
            $enfantsTuteur = $this->enfantTuteurRepository->findBy(['tuteur' => $tuteur]);
            foreach ($enfantsTuteur as $enfantTuteur) {
                $enfant = $enfantTuteur->getEnfant();
                $plaineEnfants = $this->plaineEnfantRepository->findBy(['plaine' => $plaine, 'enfant' => $enfant]);
                $coutParEnfant = 0;
                if (count($plaineEnfants) > 0) {
                    $data[$tuteur->getId()]['enfants'][$enfant->getId()]['enfant'] = $enfant;
                    foreach ($plaineEnfants as $plaineEnfant) {
                        $presences = $this->plaineService->getPresences($plaineEnfant, $tuteur);
                        foreach ($presences as $presence) {
                            $this->facturePlaine->handlePresence($presence);
                            $coutParEnfant += $presence->getCout();
                        }
                        $data[$tuteur->getId()]['enfants'][$enfant->getId()]['presences'] = $presences;
                        $data[$tuteur->getId()]['enfants'][$enfant->getId()]['cout'] = $coutParEnfant;
                    }
                }
                $coutParTuteur += $coutParEnfant;
            }
            $data[$tuteur->getId()]['cout'] = $coutParTuteur;
            $coutTotal += $coutParTuteur;
        }

        return $this->render(
            'admin/default/covid.html.twig',
            [
                'plaine' => $plaine,
                'tuteurs' => $tuteurs,
                'datas' => $data,
                'coutTotal' => $coutTotal,
            ]
        );
    }

    /**
     * @return Tuteur[]
     */
    private function getTuteurs(Plaine $plaine): iterable
    {
        $plaineEnfants = $this->plaineEnfantRepository->findBy(['plaine' => $plaine]);

        $tuteurs = new ArrayCollection();
        foreach ($plaineEnfants as $plaineEnfant) {
            $presences = $this->plainePresenceRepository->findBy(['plaine_enfant' => $plaineEnfant]);
            foreach ($presences as $presence) {
                $tuteur = $presence->getTuteur();
                if (!$tuteurs->contains($tuteur)) {
                    $tuteurs->add($tuteur);
                }
            }
        }

        $sort = new SortUtils();
        $tuteurs = $sort->sortObjectsByName($tuteurs->toArray());

        return $tuteurs;
    }

    private function getEnfants(Tuteur $tuteur, Plaine $plaine)
    {
        $this->plainePresenceRepository->findBy(['tuteur' => $tuteur, '']);
    }
}
