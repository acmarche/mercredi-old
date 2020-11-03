<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 17/08/18
 * Time: 10:25.
 */

namespace AcMarche\Mercredi\Admin\Service;

use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Repository\EnfantTuteurRepository;
use AcMarche\Mercredi\Security\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

class TuteurUtils
{
    /**
     * @var EnfantTuteurRepository
     */
    private $enfantTuteurRepository;

    public function __construct(EnfantTuteurRepository $enfantTuteurRepository)
    {
        $this->enfantTuteurRepository = $enfantTuteurRepository;
    }

    /**
     * @return Tuteur|null
     */
    public static function getTuteurByUser(User $user)
    {
        return $user->getTuteur();
    }

    public static function hasEnfants(Tuteur $tuteur)
    {
        $enfants = [];
        $tuteur_enfants = $tuteur->getEnfants();
        foreach ($tuteur_enfants as $tuteur_enfant) {
            $enfant = $tuteur_enfant->getEnfant();
            $enfants[] = $enfant;
        }

        return $enfants;
    }

    public static function getTelephones(Tuteur $tuteur)
    {
        $telephones = '';
        $gsm = $tuteur->getGsm();
        $gsmConjoint = $tuteur->getGsmConjoint();
        $telephoneBureau = $tuteur->getTelephoneBureau();
        $telephoneBureauConjoint = $tuteur->getTelephoneBureauConjoint();
        $telephone = $tuteur->getTelephone();
        $telephoneConjoint = $tuteur->getTelephoneConjoint();

        if ($gsm or $gsmConjoint) {
            $telephones .= $gsm.' | '.$gsmConjoint;
        } elseif ($telephoneBureau or $telephoneBureauConjoint) {
            $telephones .= $telephoneBureau.' | '.$telephoneBureauConjoint;
        } else {
            $telephones .= $telephone.' | '.$telephoneConjoint;
        }

        return $telephones;
    }

    public static function hasTelephone(Tuteur $tuteur): bool
    {
        if ($tuteur->getGsm()) {
            return true;
        }

        if ($tuteur->getGsmConjoint()) {
            return true;
        }

        if ($tuteur->getTelephoneBureau()) {
            return true;
        }

        if ($tuteur->getTelephoneBureauConjoint()) {
            return true;
        }

        if ($tuteur->getTelephone()) {
            return true;
        }

        if ($tuteur->getTelephoneConjoint()) {
            return true;
        }

        return false;
    }

    public static function coordonneesIsComplete(Tuteur $tuteur)
    {
        if (false === self::hasTelephone($tuteur)) {
            return false;
        }

        if (!$tuteur->getNom()) {
            return false;
        }

        if (!$tuteur->getPrenom()) {
            return false;
        }

        if (!$tuteur->getAdresse()) {
            return false;
        }

        if (!$tuteur->getCodePostal()) {
            return false;
        }

        if (!$tuteur->getLocalite()) {
            return false;
        }

        return true;
    }

    /**
     * Retourne un tableau de string contentant les emails.
     *
     * @param Tuteur[]|ArrayCollection $tuteurs
     *
     * @return array
     */
    public function getEmails($tuteurs)
    {
        $emails = [];
        foreach ($tuteurs as $tuteur) {
            if ($this->tuteurIsActif($tuteur)) {
                $emails = array_merge($emails, $this->getEmailsOfOneTuteur($tuteur));
            }
        }

        return array_unique($emails);
    }

    /**
     * Retourne la liste des tuteurs qui n'ont pas d'emails.
     *
     * @param Tuteur[]|ArrayCollection $tuteurs
     *
     * @return Tuteur[]
     */
    public function filterTuteursWithOutEmails($tuteurs)
    {
        $data = [];
        foreach ($tuteurs as $tuteur) {
            if ($this->tuteurIsActif($tuteur)) {
                if (0 == count($this->getEmailsOfOneTuteur($tuteur))) {
                    $data[] = $tuteur;
                }
            }
        }

        return $data;
    }

    public function tuteurIsActif(Tuteur $tuteur)
    {
        return count($this->enfantTuteurRepository->getEntantsActif($tuteur));
    }

    /**
     * @return array
     */
    public function getEmailsOfOneTuteur(Tuteur $tuteur)
    {
        $emails = [];
        if ($tuteur->getEmail()) {
            if (filter_var($tuteur->getEmail(), FILTER_VALIDATE_EMAIL)) {
                $emails[] = $tuteur->getEmail();
            }
        } elseif ($tuteur->getUser()) {
            if (filter_var($tuteur->getUser()->getEmail(), FILTER_VALIDATE_EMAIL)) {
                $emails[] = $tuteur->getUser()->getEmail();
            }
        }

        if ($tuteur->getEmailConjoint()) {
            if (filter_var($tuteur->getEmailConjoint(), FILTER_VALIDATE_EMAIL)) {
                $emails[] = $tuteur->getEmailConjoint();
            }
        }

        return $emails;
    }

    /**
     * @param EnfantTuteur[] $relations
     * @return Tuteur[]
     */
    public static function extractTuteurs($relations)
    {
        $tuteurs = [];
        foreach ($relations as $relation) {
            $tuteurs[] = $relation->getTuteur();
        }

        return $tuteurs;
    }
}
