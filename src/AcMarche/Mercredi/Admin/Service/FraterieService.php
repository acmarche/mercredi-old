<?php

namespace AcMarche\Mercredi\Admin\Service;

use AcMarche\Mercredi\Admin\Repository\EnfantRepository;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Repository\PresenceRepository;

class FraterieService
{
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;

    public function __construct(EnfantRepository $enfantRepository, PresenceRepository $presenceRepository)
    {
        $this->enfantRepository = $enfantRepository;
        $this->presenceRepository = $presenceRepository;
    }


    /**
     * Retourne la liste des fratrie presente sur une presence
     * Je prends toute la fratrie de tous les tuteurs
     * puis je retire ceux qui ne sont pas presents
     * @param Presence $presence
     * @param null $fratries
     * @param bool $withAbsent
     * @return Enfant[]
     */
    public function getFratrieByPresence(Presence $presence, $fratries = null, $withAbsent = true)
    {
        $fratriesNew = array();

        if (!$fratries) {
            $fratries = $fratries = $this->getFratrie($presence->getEnfant());
        }

        foreach ($fratries as $fratrie) {
            $fratrieClone = clone $fratrie; //je clone sinon modifie objet pour toutes les presences

            $presenceFratrie = $this->presenceRepository->search(
                array(
                    'jour' => $presence->getJour(),
                    'tuteur' => $presence->getTuteur(),
                    'enfant' => $fratrieClone,
                    'one' => true,
                )
            );

            if ($presenceFratrie) {
                $absent = $presenceFratrie->getAbsent();
                if ($absent) {
                    $fratrieClone->setAbsent($absent);
                    if ($withAbsent) {
                        $fratriesNew[] = $fratrieClone;
                    }
                } else {
                    $fratriesNew[] = $fratrieClone;
                }
            }
        }

        return $fratriesNew;
    }

    /**
     * Retourne la fratrie
     * @param Enfant $enfant
     * @param Tuteur $tuteur
     * @return Enfant[]
     */
    public function getFratrie(Enfant $enfant, Tuteur $tuteur = null)
    {
        $fratries = $this->enfantRepository->getFratriesBy(
            $enfant,
            $tuteur
        );

        return $fratries;
    }
}
