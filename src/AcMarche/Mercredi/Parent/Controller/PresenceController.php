<?php

namespace AcMarche\Mercredi\Parent\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Repository\PaiementRepository;
use AcMarche\Mercredi\Admin\Repository\PresenceRepository;
use AcMarche\Mercredi\Admin\Service\EnfantUtils;
use AcMarche\Mercredi\Admin\Service\PresenceService;
use AcMarche\Mercredi\Admin\Service\TuteurUtils;
use AcMarche\Mercredi\Commun\Utils\DateService;
use AcMarche\Mercredi\Parent\Form\PresenceType;
use AcMarche\Mercredi\Parent\Manager\SanteManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Presence controller.
 *
 * @Route("/presences")
 * @IsGranted("ROLE_MERCREDI_PARENT")
 */
class PresenceController extends AbstractController
{
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var EnfantUtils
     */
    private $enfantUtils;
    /**
     * @var PresenceService
     */
    private $presenceService;
    /**
     * @var DateService
     */
    private $dateService;
    /**
     * @var PaiementRepository
     */
    private $paiementRepository;
    /**
     * @var SanteManager
     */
    private $santeManager;

    public function __construct(
        PresenceRepository $presenceRepository,
        PaiementRepository $paiementRepository,
        EnfantUtils $enfantUtils,
        PresenceService $presenceService,
        DateService $dateService,
        SanteManager $santeManager
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->enfantUtils = $enfantUtils;
        $this->presenceService = $presenceService;
        $this->dateService = $dateService;
        $this->paiementRepository = $paiementRepository;
        $this->santeManager = $santeManager;
    }

    /**
     * Etape 1 select enfant.
     *
     * @Route("/select/enfant", name="parent_presence_select_enfant", methods={"GET"})
     * @IsGranted("index_enfant")
     */
    public function selectEnfant()
    {
        $user = $this->getUser();
        $tuteur = TuteurUtils::getTuteurByUser($user);

        $enfants = $this->enfantUtils->getEnfantsByTuteur($tuteur);

        return $this->render('parent/presence/select_enfant.html.twig', ['enfants' => $enfants]);
    }

    /**
     * Etape 1 select enfant.
     *
     * @Route("/select/jour/{uuid}", name="parent_presence_select_jour", methods={"GET"})
     * @IsGranted("add_presence", subject="enfant")
     */
    public function selectJour(Enfant $enfant)
    {
        $em = $this->getDoctrine()->getManager();
        $jours = $em->getRepository(Jour::class)->getForParent($enfant);

        if (!$this->santeManager->isComplete($enfant)) {
            $this->addFlash('danger', 'La fiche santé de votre enfant doit être complétée');

            return $this->redirectToRoute('parent_sante_show', ['uuid' => $enfant->getUuid()]);
        }

        return $this->render(
            'parent/presence/select_jour.html.twig',
            [
                'enfant' => $enfant,
                'jours' => $jours,
            ]
        );
    }

    /**
     * @Route("/new/{uuid}/{id}", name="parent_presence_new", methods={"GET","POST"})
     * @ParamConverter("jour", class="AcMarche\Mercredi\Admin\Entity\Jour", options={"mapping":{"id" = "id"}})
     * @ParamConverter("enfant", class="AcMarche\Mercredi\Admin\Entity\Enfant", options={"mapping":{"uuid" = "uuid"}})
     * @IsGranted("add_presence", subject="enfant")
     */
    public function new(Request $request, Enfant $enfant, Jour $jour)
    {
        if (!$this->santeManager->isComplete($enfant)) {
            $this->addFlash('danger', 'La fiche santé de votre enfant doit être complétée');

            return $this->redirectToRoute('parent_sante_show', ['uuid' => $enfant->getUuid()]);
        }

        $user = $this->getUser();
        $tuteur = TuteurUtils::getTuteurByUser($user);

        if ($this->presenceService->presenceExist($enfant, $jour)) {
            $this->addFlash('danger', 'Cette enfant est déjà inscrit à cette date');

            return $this->redirectToRoute('parent_enfants');
        }

        if (!$this->dateService->checkDate($jour->getDateJour())) {
            $content = $this->renderView('parent/presence/error_delais.txt.twig', ['jour' => $jour]);
            $this->addFlash('danger', $content);

            return $this->redirectToRoute('parent_enfants');
        }

        $presence = $this->presenceService->initPresence($enfant, $user);
        $presence->setTuteur($tuteur);

        $form = $this->createForm(PresenceType::class, $presence)
            ->add('submit', SubmitType::class, ['label' => 'Confirmer sa présence']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $presence->setJour($jour);

            $this->presenceRepository->insert($presence);

            $this->addFlash('success', 'La présence a bien été ajoutée');

            return $this->redirectToRoute('parent_enfant_show', ['uuid' => $enfant->getUuid()]);
        }

        return $this->render(
            'parent/presence/new.html.twig',
            [
                'enfant' => $enfant,
                'entity' => $presence,
                'jour' => $jour,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Affiche les presences par tuteur.
     *
     * @Route("/rend/{enfant_tuteur}/{year}", name="render_presences", methods={"GET"})
     *
     * @param $year
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderPresence(EnfantTuteur $enfant_tuteur, $year)
    {
        $enfant = $enfant_tuteur->getEnfant();

        /**
         * Pour chaque parent je vais chercher
         * les presences.
         */
        $presences_by_tuteur = $this->presenceRepository->getByEnfantTuteur($enfant_tuteur, $year);

        /*
         * Pour chaque presence je vais enregistrer
         * le detail du calcul
         */
        foreach ($presences_by_tuteur as $presence) {
            $calcul = $this->presenceService->calculCout($presence, $enfant);
            $presence->setCalcul($calcul);
            $enfant_tuteur->addPresence($presence);
        }

        /**
         * pour le tuteur, je vais chercher les paiements de l'enfant
         * et je memorise la liste pour la reafficher.
         */
        $paiements = $this->paiementRepository->getByEnfantTuteur($enfant_tuteur);
        $enfant_tuteur->addPaiements($paiements);

        /**
         * Je trie les présences par mois.
         */
        $prencesGroupByMonth = [];
        /**
         * @var Presence[]
         */
        $presences = $enfant_tuteur->getPresences();

        if (null != $presences) {
            foreach ($presences as $presence) {
                $jour = $presence->getJour();
                $mois = $jour->getDateJour()->format('n');
                $prencesGroupByMonth[$mois][] = $presence;
            }

            krsort($prencesGroupByMonth);
        }

        return $this->render(
            'parent/presence/render_presence.html.twig',
            [
                'enfant' => $enfant,
                'enfant_tuteur' => $enfant_tuteur,
                'prencesgroupbymonth' => $prencesGroupByMonth,
            ]
        );
    }
}
