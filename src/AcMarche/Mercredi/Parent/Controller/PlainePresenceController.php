<?php

namespace AcMarche\Mercredi\Parent\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Service\FacturePlaine;
use AcMarche\Mercredi\Admin\Service\MailerService;
use AcMarche\Mercredi\Admin\Service\TuteurUtils;
use AcMarche\Mercredi\Parent\Form\PlainePresenceType;
use AcMarche\Mercredi\Parent\Manager\SanteManager;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Plaine\Service\PlaineService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * PlainePresence controller.
 *
 * @Route("/plainepresence")
 * @IsGranted("ROLE_MERCREDI_PARENT")
 */
class PlainePresenceController extends AbstractController
{
    /**
     * @var MailerService
     */
    private $mailerService;
    /**
     * @var PlaineService
     */
    private $plaineService;
    /**
     * @var FacturePlaine
     */
    private $facturePlaine;
    /**
     * @var SanteManager
     */
    private $santeManager;

    public function __construct(
        MailerService $mailerService,
        PlaineService $plaineService,
        FacturePlaine $facturePlaine,
        SanteManager $santeManager
    ) {
        $this->mailerService = $mailerService;
        $this->plaineService = $plaineService;
        $this->facturePlaine = $facturePlaine;
        $this->santeManager = $santeManager;
    }

    /**
     * @Route("/plaine/{id}", name="parent_plaine_inscription", methods={"GET"})
     * @IsGranted("index_enfant")
     */
    public function show(
        Plaine $plaine
    ) {
        $user = $this->getUser();
        $tuteur = TuteurUtils::getTuteurByUser($user);

        $enfant_tuteurs = $tuteur->getEnfants();
        $enfants = [];
        $totalAPayer = 0;
        $ordreCalcule = 0;

        foreach ($enfant_tuteurs as $enfant_tuteur) {
            $enfant = $enfant_tuteur->getEnfant();
            if ($plaineEnfant = $this->plaineService->enfantExistInPlaine($enfant, $plaine)) {
                $total = 0;
                $presences = $this->plaineService->getPresences($plaineEnfant, $tuteur);

                foreach ($presences as $presence) {
                    $this->facturePlaine->handlePresence($presence);
                    $total += $presence->getCout();
                    $ordreCalcule = $presence->getOrdre();
                }
                $enfant->setInscrit(true);
                $enfant->setPresencesPlaine($presences);
                $enfant->setTotalPlaine($total);
                $enfant->setOrdre($ordreCalcule);
                $totalAPayer += $total;
            }
            $enfants[] = $enfant;
        }

        return $this->render(
            'parent/plaine_presence/show.html.twig',
            [
                'plaine' => $plaine,
                'enfants' => $enfants,
                'totalAPayer' => $totalAPayer,
            ]
        );
    }

    /**
     * Creates a new PlainePresence entity.
     *
     * @Route("/enfant/{uuid}/plaine/{plaineid}", name="parent_plainepresence_create", methods={"GET","POST"})
     * @Entity("enfant", expr="repository.findOneByUuid(uuid)")
     * @Entity("plaine", expr="repository.find(plaineid)")
     * @IsGranted("add_presence", subject="enfant")
     */
    public function new(Request $request, Enfant $enfant, Plaine $plaine)
    {
        $user = $this->getUser();
        $tuteur = TuteurUtils::getTuteurByUser($user);

        if (!$this->santeManager->isComplete($enfant)) {
            $this->addFlash('danger', 'La fiche santé de votre enfant doit être complétée');

            return $this->redirectToRoute('parent_sante_show', ['uuid' => $enfant->getUuid()]);
        }

        $form = $this->createForm(PlainePresenceType::class, [], ['plaine' => $plaine, 'enfant' => $enfant])
            ->add('submit', SubmitType::class, ['label' => 'Inscrire']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $jours = $data['jours'];

            if (0 == count($jours)) {
                $this->addFlash('danger', 'Aucun jour sélectionné');

                return $this->redirectToRoute(
                    'parent_plainepresence_create',
                    [
                        'uuid' => $enfant->getUuid(),
                        'plaineid' => $plaine->getId(),
                    ]
                );
            }

            $plaineEnfant = $this->plaineService->addEnfantToPlaine($enfant, $plaine);
            $plainePresence = $this->plaineService->createPlainePresence($plaineEnfant, $tuteur, $user);

            $this->plaineService->addPresences($plainePresence, $jours);

            $this->addFlash('success', "L' enfant a bien été inscrit");
            $this->addFlash(
                'warning',
                "L'inscription sera effective une fois que le paiement aura été effectué."
            );

            $this->mailerService->sendTuteurAddEnfantToPlaine($enfant, $tuteur, $plaine, $user);

            return $this->redirectToRoute(
                'parent_plaine_inscription',
                ['id' => $plaine->getId()]
            );
        }

        return $this->render(
            'parent/plaine_presence/new.html.twig',
            [
                'plaine' => $plaine,
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }
}
