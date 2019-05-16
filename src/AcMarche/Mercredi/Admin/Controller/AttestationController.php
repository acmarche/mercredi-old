<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Entity\Paiement;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Attestation controller.
 *
 * @Route("/attestation")
 * @IsGranted("ROLE_MERCREDI_READ")
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
     * Pdf
     * @Route("/{tuteurslugname}/{enfantslugname}/{annee}", name="tuteur_attestation", methods={"GET"})
     * @Route("/all/{annee}", name="tuteurs_attestation", methods={"GET"})
     * Entity("enfant", expr="repository.findBy(enfant)")
     * @ParamConverter("tuteur", class="AcMarche\Mercredi\Admin\Entity\Tuteur", options={"mapping": {"tuteurslugname": "slugname" }})
     * @ParamConverter("enfant", class="AcMarche\Mercredi\Admin\Entity\Enfant", options={"mapping": {"enfantslugname": "slugname" }})
     *
     */
    public function index(Request $request, Tuteur $tuteur = null, Enfant $enfant = null, $annee)
    {
        $em = $this->getDoctrine()->getManager();
        $name = 'Attestations-'.$annee;
        $html = $this->renderView('admin/tuteur/fiscale/head.html.twig', array());

        if (!$tuteur or !$enfant) {
            $args = array();
            $enfantsTuteur = $em->getRepository(EnfantTuteur::class)->search($args);

            foreach ($enfantsTuteur as $enfantTuteur) {
                $html .= $this->getHtml($enfantTuteur, $annee);
            }
        } else {
            $args = array('enfant_id' => $enfant->getId(), 'tuteur_id' => $tuteur->getId(), 'one' => true);
            $enfantTuteur = $em->getRepository(EnfantTuteur::class)->search($args);
            /**
             * Relation parent enfant
             */
            if (!$enfantTuteur) {
                $this->addFlash('danger', "Relation parent enfant non trouve");

                return $this->redirectToRoute('tuteur_show', array('slugname' => $tuteur->getSlugname()));
            }

            $name = $enfant->getSlugname().'-'.$annee;

            $html .= $this->getHtml($enfantTuteur, $annee);
        }

        $html .= $this->renderView('admin/tuteur/fiscale/foot.html.twig', array());

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
         * Paiements
         */
        $paiments = $em->getRepository(Paiement::class)->getByEnfantTuteur($enfantTuteur, $annee);

        $totalPaiement = 0;
        foreach ($paiments as $paiment) {
            $totalPaiement += $paiment->getMontant();
        }

        /**
         * jours de gardes
         */
        $onlyPaye = true;
        $gardes = $em->getRepository(Presence::class)->getByEnfantTuteur($enfantTuteur, $annee, $onlyPaye);
        $presences = array();
        foreach ($gardes as $presence) {
            $presences[] = $presence->getJour();
        }

        /**
         * get presences plaines
         */
        $args = array('enfant' => $enfant, 'tuteur' => $tuteur, 'date' => $annee, 'onlypaye' => true);
        $presencesPlaines = $em->getRepository(PlainePresence::class)->getPresences($args);
        // var_dump($presencesPlaines);

        foreach ($presencesPlaines as $presence) {
            $presences[] = $presence->getJour();
        }

        if (count($paiments) == 0) {
            return 'Aucun paiement en '.$annee.'<div class="page-breaker"></div>';
        }

        $html = $this->renderView(
            'admin/tuteur/fiscale/content.html.twig',
            array(
                'tuteur' => $tuteur,
                'enfant' => $enfant,
                'annee' => $annee,
                'presences' => $presences,
                'totalpaiement' => $totalPaiement,
            )
        );

        return $html;
    }
}
