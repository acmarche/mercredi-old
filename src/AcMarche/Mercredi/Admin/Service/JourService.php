<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 17/08/18
 * Time: 10:32
 */

namespace AcMarche\Mercredi\Admin\Service;


use AcMarche\Mercredi\Admin\Repository\JourRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineJourRepository;
use Symfony\Component\Translation\TranslatorInterface;

class JourService
{
    /**
     * @var PlaineJourRepository
     */
    private $plaineJourRepository;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var JourRepository
     */
    private $jourRepository;

    public function __construct(
        PlaineJourRepository $plaineJourRepository,
        JourRepository $jourRepository,
        TranslatorInterface $translator
    ) {
        $this->plaineJourRepository = $plaineJourRepository;
        $this->translator = $translator;
        $this->jourRepository = $jourRepository;
    }

    /**
     * Je vais chercher toutes les dates pour remplir le formulaire
     * pour le listing presence
     * @return array
     *
     */
    public function getAllDaysGardesAndPlaines()
    {
        $plaines_jours = $this->plaineJourRepository->findAll();
        $presence_jours = $this->jourRepository->findAll();

        $jours = array();

        foreach ($plaines_jours as $jour) {
            $jour_id = $jour->getId().'_plaine';
            $jourDateTime = $jour->getDateJour();
            $jourFr = $this->translator->trans($jourDateTime->format("D"));
            $jours[$jour_id] = $jourDateTime->format("d-m-Y")." ".$jourFr." (P)";
        }

        foreach ($presence_jours as $jour) {
            $jourDateTime = $jour->getDateJour();
            $jour_id = $jour->getId().'_mercredi';
            $jourFr = $this->translator->trans($jourDateTime->format("D"));
            $jours[$jour_id] = $jourDateTime->format("d-m-Y")." ".$jourFr;
        }

        uasort(
            $jours,
            function ($a, $b) {
                list($date1, $jour1) = explode(' ', $a);
                list($date2, $jour2) = explode(' ', $b);

                $ad = strtotime($date1);
                $bd = strtotime($date2);
                if ($ad == $bd) {
                    return 0;
                }

                return $ad < $bd ? 1 : -1;
            }
        );

        return $jours;
    }
}