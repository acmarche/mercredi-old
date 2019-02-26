<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 20/08/18
 * Time: 14:16
 */

namespace AcMarche\Mercredi\Commun\Utils;


use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Admin\Entity\Tuteur;

class SortUtils
{
    /**
     * @param Enfant[]|Tuteur[] $data
     *
     * @return Enfant[]|Tuteur[]
     */
    public function sortObjectsByName($data)
    {
        /**
         * Je trie le tableau enfants
         */
        usort(
            $data,
            function ($a, $b) {
                $personA = $a->getNom();
                $personB = $b->getNom();

                if ($personA == $personB) {
                    if ($a->getPrenom() > $b->getPrenom()) {
                        return 1;
                    }

                    return 0;
                }

                return $personA > $personB ? 1 : -1;
            }
        );

        return $data;
    }

    /**
     * @param array[\DateTime] $data
     * @return array[\DateTime]
     */
    public function sortByDateTime($data)
    {
        uksort(
            $data,
            function ($a, $b) {
                $dateA = \DateTime::createFromFormat('d-m-Y', $a);
                $dateA->format('Y-m-d');

                $dateB = \DateTime::createFromFormat('d-m-Y', $b);
                $dateB->format('Y-m-d');

                if ($dateA == $dateB) {
                    return 0;
                }

                return $dateA > $dateB ? 1 : -1;
            }
        );

        return $data;
    }

    /**
     * @param SanteQuestion[] $data
     *
     * @return SanteQuestion[]
     */
    public function sortObjectsByDisplayOrder($data)
    {
        usort(
            $data,
            function ($a, $b) {
                $personA = $a->getDisplayOrder();
                $personB = $b->getDisplayOrder();

                if ($personA == $personB) {
                    return 0;
                }

                return $personA > $personB ? 1 : -1;
            }
        );

        return $data;
    }

    /**
     * Je trie les présences par mois
     * @param EnfantTuteur $enfantTuteur
     * @return array
     */
    public function sortPresence(EnfantTuteur $enfantTuteur)
    {
        $prencesGroupByMonth = array();
        $presences = $enfantTuteur->getPresences();

        if ($presences != null) {
            foreach ($presences as $presence) {
                $jour = $presence->getJour();
                $year = $jour->getDateJour()->format('Y');
                $mois = $jour->getDateJour()->format('n');
                $prencesGroupByMonth[$year][$mois][] = $presence;
            }

            ksort($prencesGroupByMonth);
        }

        return $prencesGroupByMonth;
    }

    /**
     * @param Jour[] $jours
     * @return mixed
     */
    public function sortJours($presences)
    {
        usort(
            $presences,
            function ($a, $b) {
                $dateA = $a->getDateJour();
                $dateB = $b->getDateJour();

                if ($dateA == $dateB) {
                    return 0;
                }

                return $dateA > $dateB ? -1 : 1;
            }
        );

        return $presences;
    }
}