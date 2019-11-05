<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 12/11/18
 * Time: 13:26.
 */

namespace AcMarche\Mercredi\Admin\Service;

use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Repository\EnfantTuteurRepository;

class OrdreService
{
    /**
     * @var EnfantTuteurRepository
     */
    private $enfantTuteurRepository;
    /**
     * @var FraterieService
     */
    private $fraterieService;

    public function __construct(EnfantTuteurRepository $enfantTuteurRepository, FraterieService $fraterieService)
    {
        $this->enfantTuteurRepository = $enfantTuteurRepository;
        $this->fraterieService = $fraterieService;
    }

    /**
     * Pour obtenir l'ordre, j'ai besoin de la fratrie ce jour la.
     *
     * @param $fratries
     */
    public function getOrdre(Presence $presence, $fratries = null): int
    {
        $enfant = $presence->getEnfant();
        //si ordre sur presence
        if ($presence->getOrdre()) {
            return $presence->getOrdre();
        }
        $ordreBase = $enfant->getOrdre();

        //si ordre definit dans la relation tuteur enfant
        $tuteur = $presence->getTuteur();
        $args = ['enfant' => $enfant, 'tuteur' => $tuteur];
        $enfantTuteur = $this->enfantTuteurRepository->findOneBy($args);

        if ($enfantTuteur->getOrdre()) {
            $ordreBase = $enfantTuteur->getOrdre();
        }

        /*
         * ordre change pas quand vaut 1
         */
        if (1 == $ordreBase) {
            return $ordreBase;
        }

        if (!$fratries) {
            $fratries = $this->fraterieService->getFratrieByPresence($presence, null, false);
        }

        /**
         * s'il y a des fratries le meme jour
         * l'ordre va changer.
         */
        $countFratrie = count($fratries);
        //   echo "count $countFratrie \n ";

        /*
         * l'enfant est seul
         */
        if (0 == $countFratrie) {
            return 1;
        }

        /*
         * si arwen en 3 et que au moins 2 fratries
         */
        if (3 == $ordreBase && $countFratrie > 1) {
            return 3;
        }

        /*
         * si arwen en 3 et que lisa la
         */
        if (3 == $ordreBase && 1 == $countFratrie) {
            $frere = $fratries[0];
            //deux enfants à 3, un des deux doit passer a un
            if ($ordreBase == $frere->getOrdre()) {
                if ($enfant->getBirthday() < $frere->getBirthday()) {
                    return 1;
                }
            }

            return 2;
        }

        /*
         * si lisa en 2 et arwen en 3
         * count = 1
         * si marie en 1 lisa en 2
         * count = 1
         */
        if ($ordreBase > $countFratrie) {
            //     echo $enfant->getPreNom() . ' \n ';
            //      echo "count $countFratrie \n";

            foreach ($fratries as $fratrie) {
                $ordreFratrie = $fratrie->getOrdre();
                //si marie en 1, lisa en 2
                if (2 == $ordreBase && 1 == $ordreFratrie) {
                    return 2;
                }

                //si lisa en 2 et zora 3
                if ($ordreBase < $ordreFratrie) {
                    return $ordreBase - 1;
                }
            }
        }

        return $ordreBase;
    }
}
