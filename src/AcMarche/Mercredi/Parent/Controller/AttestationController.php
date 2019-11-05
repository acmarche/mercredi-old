<?php

namespace AcMarche\Mercredi\Parent\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Entity\Paiement;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Service\TuteurUtils;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Attestation controller.
 *
 * @Route("/attestation")
 * @IsGranted("ROLE_MERCREDI_PARENT")
 */
class AttestationController extends AbstractController
{
    /**
     * @var Pdf
     */
    private $pdf;

    public function __construct(Pdf $pdf)
    {
        $this->pdf = $pdf;
    }

    /**
     * Pdf.
     *
     * @Route("/{uuid}/{annee}", name="parent_attestation", methods={"GET"})
     */
    public function index(Enfant $enfant, $annee)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $tuteur = TuteurUtils::getTuteurByUser($user);

        $html = $this->renderView('admin/tuteur/fiscale/head.html.twig', []);

        $args = ['enfant' => $enfant, 'tuteur' => $tuteur];
        $enfantTuteur = $em->getRepository(EnfantTuteur::class)->findOneBy($args);

        /*
         * Relation parent enfant
         */
        if (!$enfantTuteur) {
            $this->addFlash('danger', 'Relation parent enfant non trouve');

            return $this->redirectToRoute('parent_enfants');
        }

        $name = 'Attestations-'.$enfant->getSlugname().'-'.$annee;

        $html .= $this->getHtml($enfantTuteur, $annee);

        $html .= $this->renderView('admin/tuteur/fiscale/foot.html.twig', []);

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html),
            $name.'.pdf'
        );
    }

    private function getHtml(EnfantTuteur $enfantTuteur, $annee)
    {
        $em = $this->getDoctrine()->getManager();
        $tuteur = $enfantTuteur->getTuteur();
        $enfant = $enfantTuteur->getEnfant();

        /**
         * Paiements.
         */
        $paiments = $em->getRepository(Paiement::class)->getByEnfantTuteur($enfantTuteur, $annee);

        $totalPaiement = 0;
        foreach ($paiments as $paiment) {
            $totalPaiement += $paiment->getMontant();
        }

        /**
         * jours de gardes.
         */
        $onlyPaye = true;
        $gardes = $em->getRepository(Presence::class)->getByEnfantTuteur($enfantTuteur, $annee, $onlyPaye);
        $presences = [];
        foreach ($gardes as $presence) {
            $presences[] = $presence->getJour();
        }

        /**
         * get presences plaines.
         */
        $args = ['enfant' => $enfant, 'tuteur' => $tuteur, 'date' => $annee, 'onlypaye' => true];
        $presencesPlaines = $em->getRepository(PlainePresence::class)->getPresences($args);
        // var_dump($presencesPlaines);

        foreach ($presencesPlaines as $presence) {
            $presences[] = $presence->getJour();
        }

        if (0 == count($paiments)) {
            return 'Aucun paiement en '.$annee.'<div class="page-breaker"></div>';
        }

        $html = $this->renderView(
            'admin/tuteur/fiscale/content.html.twig',
            [
                'tuteur' => $tuteur,
                'enfant' => $enfant,
                'annee' => $annee,
                'presences' => $presences,
                'totalpaiement' => $totalPaiement,
            ]
        );

        return $html;
    }
}
