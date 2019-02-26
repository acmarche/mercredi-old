<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 9/01/18
 * Time: 13:16
 */

namespace AcMarche\Mercredi\Plaine\Service;

use AcMarche\Mercredi\Plaine\Entity\PlaineJour;
use AcMarche\Mercredi\Plaine\Repository\PlaineEnfantRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineJourRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineMaxRepository;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Security\Entity\User;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Plaine\Entity\PlaineEnfant;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;

class PlaineService
{
    /**
     * @var PlaineJourRepository
     */
    private $plaineJourRepository;
    /**
     * @var PlainePresenceRepository
     */
    private $plainePresenceRepository;
    /**
     * @var PlaineEnfantRepository
     */
    private $plaineEnfantRepository;
    /**
     * @var PlaineRepository
     */
    private $plaineRepository;
    /**
     * @var ScolaireService
     */
    private $scolaireService;
    /**
     * @var PlaineMaxRepository
     */
    private $plaineMaxRepository;

    public function __construct(
        PlaineJourRepository $plaineJourRepository,
        PlainePresenceRepository $plainePresenceRepository,
        PlaineEnfantRepository $plaineEnfantRepository,
        PlaineMaxRepository $plaineMaxRepository,
        PlaineRepository $plaineRepository,
        ScolaireService $scolaireService
    ) {
        $this->plaineJourRepository = $plaineJourRepository;
        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->plaineEnfantRepository = $plaineEnfantRepository;
        $this->plaineRepository = $plaineRepository;
        $this->scolaireService = $scolaireService;
        $this->plaineMaxRepository = $plaineMaxRepository;
    }

    public function addEnfantToPlaine(Enfant $enfant, Plaine $plaine)
    {
        if (!$plaineEnfant = $this->enfantExistInPlaine($enfant, $plaine)) {
            $plaineEnfant = new PlaineEnfant();
        }
        $plaineEnfant->setPlaine($plaine);
        $plaineEnfant->setEnfant($enfant);

        $this->plaineEnfantRepository->insert($plaineEnfant);

        return $plaineEnfant;
    }

    public function createPlainePresence(PlaineEnfant $plaineEnfant, Tuteur $tuteur, User $user)
    {
        $plainePresence = new PlainePresence();
        $plainePresence->setTuteur($tuteur);
        $plainePresence->setPlaineEnfant($plaineEnfant);
        $plainePresence->setUserAdd($user);

        return $plainePresence;
    }

    public function addPresences(PlainePresence $plainePresence, $jours)
    {
        foreach ($jours as $jour) {
            $presence = clone $plainePresence;
            $presence->setJour($jour);

            $this->plainePresenceRepository->persist($presence);
        }
        $this->plainePresenceRepository->save();
    }

    /**
     * @param Enfant $enfant
     * @param Plaine $plaine
     * @return null|PlaineEnfant
     */
    public function enfantExistInPlaine(Enfant $enfant, Plaine $plaine)
    {
        return $this->plaineEnfantRepository->findOneBy(
            [
                'enfant' => $enfant,
                'plaine' => $plaine,
            ]
        );
    }

    /**
     * @param PlaineEnfant $plaineEnfant
     * @param Tuteur $tuteur
     * @return PlainePresence[]
     */
    public function getPresences(PlaineEnfant $plaineEnfant, Tuteur $tuteur)
    {
        return $this->plainePresenceRepository->findBy(
            [
                'plaine_enfant' => $plaineEnfant,
                'tuteur' => $tuteur,
            ]
        );
    }

    /**
     * @return null|Plaine
     */
    public function getPlaineOuverte()
    {
        return $this->plaineRepository->findOneBy(
            [
                'inscription_ouverture' => true,
            ]
        );
    }

    public function getEnfantsInscritsByJour(PlaineJour $plaineJour, $groupeScolaire = null)
    {
        $groupes = [];
        $presences = $this->plainePresenceRepository->findBy(['jour' => $plaineJour]);
        foreach ($presences as $presence) {
            $enfant = $presence->getPlaineEnfant()->getEnfant();
            $groupe = $this->scolaireService->getGroupeScolaire($enfant);
            $groupes[$groupe][] = $enfant;
        }

        if ($groupeScolaire) {
            return isset($groupes[$groupeScolaire]) ? $groupes[$groupeScolaire] : [];
        }

        return $groupes;
    }

    public function getEnfantsByGroupeScolaire(Plaine $plaine, $groupeScolaire = null)
    {
        $groupes = [];
        $plaineEnfants = $this->plaineEnfantRepository->findBy(
            [
                'plaine' => $plaine,
            ]
        );

        foreach ($plaineEnfants as $plaineEnfant) {
            $enfant = $plaineEnfant->getEnfant();
            $groupe = $this->scolaireService->getGroupeScolaire($enfant);
            $groupes[$groupe][] = $enfant;
        }

        if ($groupeScolaire) {
            return isset($groupes[$groupeScolaire]) ? $groupes[$groupeScolaire] : [];
        }

        return $groupes;
    }

    public function isMaxByGroupScolaire(Enfant $enfant, PlaineJour $plaineJour)
    {
        if (!$groupeScolaire = $this->scolaireService->getGroupeScolaire($enfant)) {
            return false;
        }

        $groupes = $this->getEnfantsInscritsByJour($plaineJour);
        $nbInscrits = isset($groupes[$groupeScolaire]) ? count($groupes[$groupeScolaire]) : 0;

        $plaine = $plaineJour->getPlaine();
        $groupesMax = $this->getGroupesMax($plaine);

        $max = isset($groupesMax[$groupeScolaire]) ? $groupesMax[$groupeScolaire] : 100;

        if ($nbInscrits >= $max) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param PlainePresence $plainePresence
     * @return null|string
     */
    public function getEmailOnPresence(PlainePresence $plainePresence)
    {
        $userAdd = $plainePresence->getUserAdd();
        $tuteur = $plainePresence->getTuteur();
        if ($tuteur) {
            if ($user = $tuteur->getUser()) {
                return $user->getEmail();
            }

            if ($tuteur->getEmail()) {
                return $tuteur->getEmail();
            }

            if ($tuteur->getEmailConjoint()) {
                return $tuteur->getEmailConjoint();
            }
        }

        if ($userAdd) {
            return $userAdd->getEmail();
        }

        return null;
    }

    public function getPresenceByDateAndEnfant(\DateTimeInterface $date, Enfant $enfant): ?PlainePresence
    {
        $jour = $this->plaineJourRepository->findOneBy(['date_jour' => $date]);
        if (!$jour) {
            return null;
        }

        $plaine = $jour->getPlaine();

        if (!$plaine) {
            return null;
        }

        $plaineEnfant = $this->plaineEnfantRepository->findOneBy(['enfant' => $enfant, 'plaine' => $plaine]);
        if (!$plaineEnfant) {
            return null;
        }

        return $this->plainePresenceRepository->findOneBy(
            ['jour' => $jour, 'plaine_enfant' => $plaineEnfant]
        );
    }

    public function getGroupesMax(Plaine $plaine)
    {
        $maxs = $this->plaineMaxRepository->findBy(['plaine' => $plaine]);
        $groupes = [];
        foreach ($maxs as $max) {
            $groupes[$max->getGroupe()] = $max->getMaximum();
        }

        return $groupes;
    }

}
