<?php

namespace AcMarche\Mercredi\Animateur\Controller;

use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Admin\Form\Presence\PresenceEditType;
use AcMarche\Mercredi\Admin\Form\Presence\PresenceType;
use AcMarche\Mercredi\Admin\Repository\JourRepository;
use AcMarche\Mercredi\Admin\Repository\PresenceRepository;
use AcMarche\Mercredi\Admin\Service\EnfantUtils;
use AcMarche\Mercredi\Admin\Service\Facture;
use AcMarche\Mercredi\Admin\Service\PresenceService;
use AcMarche\Mercredi\Commun\Utils\SortUtils;
use AcMarche\Mercredi\Plaine\Entity\PlaineJour;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use AcMarche\Mercredi\Plaine\Repository\PlaineJourRepository;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Form\Search\SearchPresenceByMonthType;
use AcMarche\Mercredi\Admin\Form\Search\SearchPresenceType;


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

    public function __construct(
        JourRepository $jourRepository,
        PlaineJourRepository $plaineJourRepository,
        PresenceRepository $presenceRepository,
        PlainePresenceRepository $plainePresenceRepository,
        SortUtils $sortUtils
    ) {
        $this->plaineJourRepository = $plaineJourRepository;
        $this->presenceRepository = $presenceRepository;
        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->jourRepository = $jourRepository;
        $this->sortUtils = $sortUtils;
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
        if ($type == 'plaine') {
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

        return $this->render(
            'animateur/presence/show.html.twig',
            array(
                'jour' => $jour,
                'presences' => $presences,
            )
        );
    }


}
