<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Service\PresenceService;
use AcMarche\Mercredi\Plaine\Service\PlaineService;
use AcMarche\Mercredi\Commun\Utils\DateService;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class DefaultController
 * @package AcMarche\Admin\Admin\Controller
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class ExportController extends AbstractController
{
    /**
     * @var PlaineService
     */
    private $plaineService;
    /**
     * @var DateService
     */
    private $dateService;
    /**
     * @var PresenceService
     */
    private $presenceService;
    /**
     * @var ScolaireService
     */
    private $scolaireService;

    public function __construct(
        DateService $dateService,
        PlaineService $plaineService,
        PresenceService $presenceService,
        ScolaireService $scolaireService
    ) {
        $this->plaineService = $plaineService;
        $this->dateService = $dateService;
        $this->presenceService = $presenceService;
        $this->scolaireService = $scolaireService;
    }

    /**
     * @Route("/presencemois/xls/{mois}/{type}/{one}", name="presence_mois_xls", requirements={"mois"=".+"}, methods={"GET"})
     * Requirement a cause du format "mois/annee"
     * @param $mois
     * @param $type
     * @param bool $one Office de l'enfance
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function xls($mois, $type, bool $one)
    {
        $spreadsheet = new Spreadsheet();

        if ($one) {
            $this->createXSLOne($mois, $type, $spreadsheet);
        } else {
            $this->createXSLObject($mois, $type, $spreadsheet);
        }

        $fileName = 'listing-'.preg_replace("#/#", "-", $mois).'.xls';

        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);

        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @param string $mois
     * @param string $type
     * @param Spreadsheet $spreadsheet
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function createXSLObject(string $mois, string $type, Spreadsheet $spreadsheet)
    {
        $result = $this->presenceService->getPresencesAndEnfantsByMonth($mois, $type);
        $presences = $result['allpresences'];
        /**
         * @var Enfant[] $enfants
         */
        $enfants = $result['allenfants'];

        $sheet = $spreadsheet->getActiveSheet();

        $alphabets = range('A', 'Z');
        //  $lastLettre = $alphabets[(count($dates) * 2) - 1];
        /**
         * title
         */
        $c = 1;
        $colonne = "C";
        $sheet->setCellValue('A'.$c, 'Enfant')
            ->setCellValue('B'.$c, 'Né le');
        foreach ($presences as $date => $count) {
            $sheet->setCellValue($colonne.$c, $date);
            $colonne++;
        }
        $sheet->setCellValue($colonne.$c, "Total");

        $ligne = 3;
        foreach ($enfants as $enfant) {
            $colonne = "A";
            $count = 0;
            $enfantNom = $enfant->__toString();
            $neLe = $enfant->getBirthday() ? $enfant->getBirthday()->format('d-m-Y') : "";
            $sheet->setCellValue($colonne.$ligne, $enfantNom);
            $colonne++;
            $sheet->setCellValue($colonne.$ligne, $neLe);
            foreach ($presences as $date => $data) {
                $enfantsByDate = $data['enfants'];
                $txt = 0;
                if (in_array($enfant, $enfantsByDate)) {
                    $txt = '1';
                    $count++;
                }
                $colonne++;
                $sheet->setCellValue($colonne.$ligne, $txt);
            }
            $colonne++;
            $sheet->setCellValue($colonne.$ligne, $count);
            $ligne++;
        }
        $colonne = "A";
        $sheet->setCellValue($colonne.$ligne, count($enfants).' enfants');
        $colonne = "C";
        $totalmois = 0;
        foreach ($presences as $date => $data) {
            $sheet->setCellValue($colonne.$ligne, $data['count']);
            $totalmois += $data['count'];
            $colonne++;
        }

        $sheet->setCellValue($colonne.$ligne, $totalmois);
    }

    /**
     * @param $mois
     * @param $type
     * @param Spreadsheet $spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \Exception
     */
    private function createXSLOne($mois, $type, Spreadsheet $spreadsheet)
    {
        $sheet = $spreadsheet->getActiveSheet();

        $dateInterval = $this->dateService->getDateIntervale("01/".$mois);

        /**
         * titre de la feuille
         */
        $c = 1;
        $sheet
            ->setCellValue('A'.$c, 'Nom')
            ->setCellValue('B'.$c, 'Prénom')
            ->setCellValue('C'.$c, 'Né le')
            ->setCellValue('D'.$c, 'Groupe');

        $colonne = "E";
        foreach ($dateInterval as $date) {
            $sheet->setCellValue($colonne.$c, $date->format('D j'));
            $colonne++;
        }

        /**
         *
         */
        $result = $this->presenceService->getPresencesAndEnfantsByMonth($mois, $type);

        /**
         * @var Enfant[] $enfants
         */
        $enfants = $result['allenfants'];

        $ligne = 3;
        foreach ($enfants as $enfant) {
            $colonne = "A";
            $neLe = $enfant->getBirthday() ? $enfant->getBirthday()->format('d-m-Y') : "";
            $sheet->setCellValue($colonne.$ligne, $enfant->getNom());
            $colonne++;
            $sheet->setCellValue($colonne.$ligne, $enfant->getPrenom());
            $colonne++;
            $sheet->setCellValue($colonne.$ligne, $neLe);
            $colonne++;
            $sheet->setCellValue($colonne.$ligne, $this->scolaireService->getGroupeScolaire($enfant));

            foreach ($dateInterval as $date) {
                $presence = $this->plaineService->getPresenceByDateAndEnfant($date, $enfant);

                if (!$presence) {
                    $colonne++;
                    continue;
                }

                $colonne++;
                $sheet->setCellValue($colonne.$ligne, 1);
            }
            $ligne++;
        }
    }
}
