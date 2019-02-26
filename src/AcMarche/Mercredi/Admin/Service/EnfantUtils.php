<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 20/09/16
 * Time: 13:21
 */

namespace AcMarche\Mercredi\Admin\Service;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Tuteur;

class EnfantUtils
{
    public static function hasParents(Enfant $enfant)
    {
        $tuteurs = array();
        $enfant_tuteurs = $enfant->getTuteurs();
        foreach ($enfant_tuteurs as $enfant_tuteur) {
            $tuteur = $enfant_tuteur->getTuteur();
            $tuteurs[] = $tuteur;
        }

        return $tuteurs;
    }

    /**
     * @param Tuteur $tuteur
     * @return Enfant[]
     */
    public function getEnfantsByTuteur(Tuteur $tuteur)
    {
        $enfant_tuteurs = $tuteur->getEnfants();
        $enfants = array();

        foreach ($enfant_tuteurs as $enfant_tuteur) {
            $enfants[] = $enfant_tuteur->getEnfant();
        }

        return $enfants;
    }

    public function ficheIsComplete(Enfant $enfant)
    {
        if (!$enfant->getNom()) {
            return false;
        }

        if (!$enfant->getPrenom()) {
            return false;
        }

        if (!$enfant->getEcole()) {
            return false;
        }

        if (!$enfant->getAnneeScolaire()) {
            return false;
        }

        return true;
    }

    /**
     * @param Enfant[] $enfants
     */
    public function checkFicheEnfants($enfants)
    {
        foreach ($enfants as $enfant) {
            $enfant->setFicheComplete(self::ficheIsComplete($enfant));
        }
    }

    /**
     * @param Enfant[] $enfants
     *
     * @return Tuteur[]
     */
    public function extractTuteurs($enfants) {
        $tuteurs = [];
        foreach ($enfants as $enfant) {
            foreach ($enfant->getTuteurs() as $enfantTuteur) {
             $tuteurs[] = $enfantTuteur->getTuteur();
            }
        }
        return $tuteurs;
    }
}
