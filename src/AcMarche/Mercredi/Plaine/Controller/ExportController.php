<?php

namespace AcMarche\Mercredi\Plaine\Controller;

use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Plaine\Entity\PlaineEnfant;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Plaine controller.
 *
 * @Route("/plaine")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class ExportController extends AbstractController
{
    /**
     * @var Pdf
     */
    private $pdf;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(Pdf $pdf, ParameterBagInterface $parameterBag)
    {
        $this->pdf = $pdf;
        $this->parameterBag = $parameterBag;
    }

    /**
     * Lists all Employe entities.
     *
     * @Route("/pdf/{slugname}", name="plaine_pdf", methods={"GET"})
     */
    public function pdf(Plaine $plaine)
    {
        $em = $this->getDoctrine()->getManager();
        $images = $this->getImagesBase64();

        $dates = $plaine->getJours();
        $premat = $plaine->isPremat();
        $annee_premat = '';
        $filter_petit = ['PM', '1M', '2M'];
        //si on separe les premats le filtre petit change
        if ($premat) {
            $filter_petit = ['1M', '2M'];
            //je prend le premier jour des dates pour obtenir l'annee de la plaine
            $plaine_jour = $dates[0];
            //get datetime object
            $jour = $plaine_jour->getDateJour();
            /**
             * je clone sinon modifie objet original.
             *
             * @var \DateTime
             */
            $jour_copy = clone $jour;
            //je retire 3 ans
            $annee_plaine = $jour_copy->modify('-3 Year');
            $annee_premat = $annee_plaine->format('Y');
        }

        $groupes = [];

        /**
         * Pour chaque enfant je vais chercher ces jours de presences
         * et j'ajoutes les ids des jours presents.
         */
        $plaine_enfants = $em->getRepository(PlaineEnfant::class)->search(
            ['plaine_id' => $plaine->getId()]
        );

        foreach ($plaine_enfants as $plaine_enfant) {
            $enfant = $plaine_enfant->getEnfant();

            //il me faut au moins un tuteur pour mettre dans le pdf
            $tuteurs = $em->getRepository(EnfantTuteur::class)->getTuteursByEnfant($enfant);

            $this->getJourIdsByPresence($plaine_enfant, $tuteurs[0]);

            $annee_scolaire = $enfant->getAnneeScolaire();
            $groupe_scolaire = $enfant->getGroupeScolaire();

            if ($groupe_scolaire) {
                $groupes[$groupe_scolaire][] = $plaine_enfant;
                continue;
            }

            //si la plaine a un groupe premat
            if ($premat) {
                $birthday = $enfant->getBirthday();
                $birthdayYear = $birthday ? $birthday->format('Y') : '';
                if ($annee_premat == $birthdayYear) {
                    $groupes['premats'][] = $plaine_enfant;
                    continue;
                }
            }

            if (in_array($annee_scolaire, $filter_petit)) {
                $groupes['petits'][] = $plaine_enfant;
                continue;
            }

            if (in_array($annee_scolaire, ['3M', '1P', '2P'])) {
                $groupes['moyens'][] = $plaine_enfant;
                continue;
            }

            $groupes['grands'][] = $plaine_enfant;
        }

        $html = $this->renderView(
            'plaine/export/pdf/head.html.twig',
            [
                'plaine' => $plaine,
            ]
        );

        foreach ($groupes as $taille => $groupe) {
            $html .= $this->renderView(
                'plaine/export/pdf/line.html.twig',
                [
                    'plaine' => $plaine,
                    'taille' => ucfirst($taille),
                    'dates' => $dates,
                    'plaine_enfants' => $groupe,
                    'images' => $images,
                ]
            );
            //je remet compteur a zero
            foreach ($dates as $date) {
                $date->setEnfants(0);
                $date->setEnfantsMoins6(0);
            }
        }

        $html .= $this->renderView(
            'plaine/export/pdf/foot.html.twig',
            []
        );

        //  return new Response($html);

        $name = $plaine->getSlugname();

        $this->pdf->setOption('footer-right', '[page]/[toPage]');
        if (count($dates) > 6) {
            $this->pdf->setOption('orientation', 'landscape');
        }

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html),
            $name.'.pdf'
        );
    }

    /**
     * Ajoute un tableau d'ids des jours a la presence
     * de l'enfant.
     *
     * @param PlaineEnfant $plaine_enfant
     *
     * @return PlaineEnfant
     */
    private function getJourIdsByPresence($plaine_enfant, $tuteur)
    {
        /**
         * Pour savoir si present ou pas.
         */
        $presences_object = $plaine_enfant->getPresences();
        $presence_jour_ids = [];
        foreach ($presences_object as $object) {
            $absent = $object->getAbsent();
            if (!$absent) {
                $presence_jour_ids[] = $object->getJour()->getId();
            }
        }

        $plaine_enfant->addJourIds($presence_jour_ids);
        if ($tuteur) {
            $plaine_enfant->setTuteur($tuteur);
        }

        return $plaine_enfant;
    }

    private function getImagesBase64()
    {
        $root = $this->getParameter(
                'mercredi.project_dir'
            ).DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR;
        $ok = $root.'check_ok.jpg';
        $ko = $root.'check_ko.jpg';
        $data = [];
        $data['ok'] = base64_encode(file_get_contents($ok));
        $data['ko'] = base64_encode(file_get_contents($ko));

        return $data;
    }
}
