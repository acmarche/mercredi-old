<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 16/08/18
 * Time: 17:23
 */

namespace AcMarche\Mercredi\Commun\Utils;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Service\TuteurUtils;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;

class ScolaireService
{
    /**
     * @var SortUtils
     */
    private $sortUtils;

    public function __construct(SortUtils $sortUtils)
    {
        $this->sortUtils = $sortUtils;
    }

    public static function getAnneesScolaires()
    {
        $annees = ["PM", "1M", "2M", "3M", "1P", "2P", "3P", "4P", "5P", "6P"];

        return array_combine($annees, $annees);
    }

    public function getGroupeScolaire(Enfant $enfant)
    {
        $annee_scolaire = $enfant->getAnneeScolaire();

        if (in_array($annee_scolaire, array("PM", "1M", "2M"))) {
            return 'petits';
        } elseif (in_array($annee_scolaire, array("3M", "1P", "2P"))) {
            return 'moyens';
        } else {
            return 'grands';
        }
    }

    public static function getGroupesScolaires()
    {
        $groupes = array('premats', 'petits', 'moyens', 'grands');

        return array_combine($groupes, $groupes);
    }

    /**
     * @param Presence[]|PlainePresence[] $presences
     *
     * @return array
     */
    public function groupPresences($presences, string $type)
    {
        $petits = $moyens = $grands = array();

        foreach ($presences as $presence) {
            if ($type == 'plaine') {
                $plaine_enfant = $presence->getPlaineEnfant();
                $enfant = $plaine_enfant->getEnfant();
            } else {
                $enfant = $presence->getEnfant();
            }

            $enfantTuteurs = $enfant->getTuteurs();
            $telephones = '';

            foreach ($enfantTuteurs as $enfantTuteur) {
                $tuteur = $enfantTuteur->getTuteur();
                $telephones = TuteurUtils::getTelephones($tuteur);
            }

            $enfant->setTelephones($telephones);

            if ($presence->getRemarques()) {
                $enfant->setRemarques($enfant->getRemarques().' (Parent=>) '.$presence->getRemarques());
            }

            $groupe = $this->getGroupeScolaire($enfant);
            switch ($groupe) {
                case 'petits':
                    $petits[] = $enfant;
                    break;
                case 'moyens':
                    $moyens[] = $enfant;
                    break;
                default:
                    $grands[] = $enfant;
                    break;
            }
        }

        $petits = $this->sortUtils->sortObjectsByName($petits);
        $moyens = $this->sortUtils->sortObjectsByName($moyens);
        $grands = $this->sortUtils->sortObjectsByName($grands);

        return ['petits' => $petits, 'moyens' => $moyens, 'grands' => $grands];
    }


}