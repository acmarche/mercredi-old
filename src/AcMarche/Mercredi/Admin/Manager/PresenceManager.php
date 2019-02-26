<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 27/11/18
 * Time: 9:29
 */

namespace AcMarche\Mercredi\Admin\Manager;


use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Repository\EnfantTuteurRepository;
use AcMarche\Mercredi\Admin\Repository\PresenceRepository;

class PresenceManager
{
    /**
     * @var EnfantTuteurRepository
     */
    private $enfantTuteurRepository;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;

    public function __construct(EnfantTuteurRepository $enfantTuteurRepository, PresenceRepository $presenceRepository)
    {
        $this->enfantTuteurRepository = $enfantTuteurRepository;
        $this->presenceRepository = $presenceRepository;
    }

    public function getPresencesNonPayes(Tuteur $tuteur)
    {
        $enfant_tuteurs = $this->enfantTuteurRepository->findBy(
            ['tuteur' => $tuteur]
        );

        $presences = [];

        foreach ($enfant_tuteurs as $enfantTuteur) {
            $enfant = $enfantTuteur->getEnfant();

            $nonpayes = $this->presenceRepository->getPresencesNonPayesNew($enfant, $tuteur);
            if ($nonpayes) {
                $presences = array_merge($presences, $nonpayes);
            }
        }

        return $presences;
    }
}