<?php

namespace AcMarche\Mercredi\Admin\Service;

class EnfanceData
{
    public static function getSexes()
    {
        return ['Masculin' => 'Masculin', 'Féminin' => 'Féminin'];
    }

    public static function getCivilites()
    {
        return ['Monsieur' => 'Monsieur', 'Madame' => 'Madame'];
    }

    public static function getTypePaiement()
    {
        $paiements = ['Abonnement', 'Journée pédagogique', 'Plaine', 'Mercredi'];

        return array_combine($paiements, $paiements);
    }

    public static function getModePaiement()
    {
        $modePaiements = ['Bancontact', 'Liquide', 'Virement'];

        return array_combine($modePaiements, $modePaiements);
    }

    public static function getOrdres()
    {
        return [1 => 1, 2 => 2, 'Suivant' => 3];
    }

    public static function getListAbsences()
    {
        return [
            0 => 'Non',
            1 => 'Oui avec certificat',
            -1 => 'Oui sans certificat',
        ];
    }

    public static function getAbsenceTxt($number = false)
    {
        $absences = self::getListAbsences();
        //attention si number = 0
        if (false !== $number) {
            return isset($absences[$number]) ? $absences[$number] : $number;
        }

        /*
         * inverse clef valeur pour le form
         */
        return array_flip($absences);
    }

    public static function getTailleTshirt()
    {
        return array_flip(['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL']);
    }

    public static function getColors()
    {
        $colors = [
            'Vert' => '#7bd148',
            'Bleu foncé' => '#5484ed',
            'Bleu' => '#a4bdfc',
            'Turquoise' => '#46d6db',
            'Vert clair' => '#7ae7bf',
            'Vert foncé' => '#51b749',
            'Jaune' => '#fbd75b',
            'Orange' => '#ffb878',
            'Rouge' => '#ff887c',
            'Rouge foncé' => '#dc2127',
            'Mauve' => '#dbadff',
            'Gris' => '#e1e1e1',
        ];

        ksort($colors);

        return $colors;
    }

    /**
     * @return array
     */
    public static function getTypesCompte()
    {
        $types = ['Parent', 'Ecole'];

        return array_combine($types, $types);
    }
}
