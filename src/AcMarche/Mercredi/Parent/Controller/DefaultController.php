<?php

namespace AcMarche\Mercredi\Parent\Controller;

use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Manager\PresenceManager;
use AcMarche\Mercredi\Admin\Repository\EnfantTuteurRepository;
use AcMarche\Mercredi\Admin\Repository\PresenceRepository;
use AcMarche\Mercredi\Admin\Service\EnfantUtils;
use AcMarche\Mercredi\Admin\Service\TuteurUtils;
use AcMarche\Mercredi\Parent\Manager\SanteManager;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Plaine\Service\PlaineService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class DefaultController.
 */
class DefaultController extends AbstractController
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var EnfantUtils
     */
    private $enfantUtils;
    /**
     * @var PlaineService
     */
    private $plaineService;
    /**
     * @var SanteManager
     */
    private $santeManger;
    /**
     * @var EnfantTuteurRepository
     */
    private $enfantTuteurRepository;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var PlainePresenceRepository
     */
    private $plainePresenceRepository;
    /**
     * @var PresenceManager
     */
    private $presenceManager;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        PresenceManager $presenceManager,
        PlainePresenceRepository $plainePresenceRepository,
        EnfantUtils $enfantUtils,
        PlaineService $plaineService,
        SanteManager $santeManager
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->enfantUtils = $enfantUtils;
        $this->plaineService = $plaineService;
        $this->santeManger = $santeManager;

        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->presenceManager = $presenceManager;
    }

    /**
     * @Route("/", name="home_parent")
     */
    public function index()
    {
        if (!$this->authorizationChecker->isGranted('ROLE_MERCREDI_PARENT')) {
            $this->addFlash('danger', 'Accès refusé');

            return $this->redirectToRoute('homepage');
        }

        $user = $this->getUser();

        if (!$user->getTuteur() instanceof Tuteur) {
            return $this->redirectToRoute('parent_nouveau');
        }

        $tuteur = TuteurUtils::getTuteurByUser($user);
        $tuteurIsComplete = TuteurUtils::coordonneesIsComplete($tuteur);

        $presencesPlaines = $presences = $enfants = [];
        $plaine = null;

        $enfants = $this->enfantUtils->getEnfantsByTuteur($tuteur);

        $this->santeManger->isCompleteForEnfants($enfants);
        $this->enfantUtils->checkFicheEnfants($enfants);

        $presences = $this->presenceManager->getPresencesNonPayes($tuteur);

        $args = ['tuteur' => $tuteur];
        if ($presencePlaine = $this->plainePresenceRepository->getPresencesNonPayes($args)) {
            $presencesPlaines[] = $presencePlaine;
        }

        $plaine = $this->plaineService->getPlaineOuverte();

        $year = date('Y') - 1;

        return $this->render(
            'parent/default/index.html.twig',
            [
                'presences' => $presences,
                'enfants' => $enfants,
                'presencesPlaines' => $presencesPlaines,
                'plaine' => $plaine,
                'tuteurIsComplete' => $tuteurIsComplete,
                'year' => $year,
            ]
        );
    }

    /**
     * @Route("/nouveau", name="parent_nouveau")
     */
    public function nouveau()
    {
        return $this->render('parent/default/nouveau.html.twig', []);
    }
}
