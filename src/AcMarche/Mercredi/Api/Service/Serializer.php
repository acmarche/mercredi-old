<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 21/01/19
 * Time: 17:05.
 */

namespace AcMarche\Mercredi\Api\Service;

use AcMarche\Mercredi\Admin\Entity\Ecole;
use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Admin\Entity\Paiement;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Admin\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Admin\Entity\Sante\SanteReponse;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Commun\Utils\DateService;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Security\Entity\User;

class Serializer
{
    /**
     * @var DateService
     */
    private $dateService;

    public function __construct(DateService $dateService)
    {
        $this->dateService = $dateService;
    }

    /**
     * @param Jour[] $jours
     *
     * @return Jour[]
     */
    public function serializeJour(iterable $jours, Enfant $enfant)
    {
        $data = [];
        foreach ($jours as $jour) {
            $date = $jour->getDateJour();
            $std = new \stdClass();
            $std->id = $jour->getId();
            $std->enfantId = $enfant->getId();
            $std->date_jour = $date->format('Y-m-d');
            $std->date_jour_fr = $this->dateService->getFr($date, true);
            $std->prix1 = $jour->getPrix1();
            $std->prix2 = $jour->getPrix2();
            $std->prix3 = $jour->getPrix3();
            $std->color = $jour->getColor();
            $std->remarques = $jour->getRemarques();
            $data[] = $std;
        }

        return $data;
    }

    /**
     * @return Tuteur|\stdClass
     */
    public function serializeTuteur(Tuteur $tuteur)
    {
        $std = new \stdClass();
        $std->id = $tuteur->getId();
        $std->nom = $tuteur->getNom();
        $std->prenom = $tuteur->getPrenom();
        $std->adresse = $tuteur->getAdresse();
        $std->code_postal = $tuteur->getCodePostal();
        $std->localite = $tuteur->getLocalite();
        $std->telephone = $tuteur->getTelephone();
        $std->telephone_bureau = $tuteur->getTelephoneBureau();
        $std->email = $tuteur->getEmail();
        $std->gsm = $tuteur->getGsm();
        $std->slugname = $tuteur->getSlugname();
        $std->remarque = $tuteur->getRemarques();

        $std->nom_conjoint = $tuteur->getNomConjoint();
        $std->prenom_conjoint = $tuteur->getPrenomConjoint();
        $std->email_conjoint = $tuteur->getEmailConjoint();
        $std->telephone_conjoint = $tuteur->getTelephoneConjoint();
        $std->telephone_bureau_conjoint = $tuteur->getTelephoneBureauConjoint();
        $std->gsm_conjoint = $tuteur->getGsmConjoint();

        return $std;
    }

    /**
     * @param Enfant[] $enfants
     *
     * @return Enfant[]
     */
    public function serializeEnfant(iterable $enfants)
    {
        $data = [];
        foreach ($enfants as $enfant) {
            $std = new \stdClass();
            $std->id = $enfant->getId();
            $std->nom = $enfant->getNom();
            $std->prenom = $enfant->getPrenom();
            $std->numero_national = $enfant->getNumeroNational();
            $std->birthday = $enfant->getBirthday()->format('Y-m-d');
            $ecole = $enfant->getEcole();
            $std->ecole_id = $ecole ? $ecole->getId() : 0;
            $std->annee_scolaire = $enfant->getAnneeScolaire();
            $std->sexe = $enfant->getSexe();
            $std->remarques = $enfant->getRemarques();
            $std->photo_url = 'http://lorempixel.com/80/80/people/';
            $data[] = $std;
        }

        return $data;
    }

    /**
     * @param Presence[] $presences
     *
     * @return Presence[]
     */
    public function serializePresences(iterable $presences)
    {
        $data = [];
        foreach ($presences as $presence) {
            $std = new \stdClass();
            $std->id = $presence->getId();
            $std->enfantId = $presence->getEnfant()->getId();
            $std->absent = $presence->getAbsent() ? true : false;
            $std->payer = $presence->getPaiement() ? true : false;
            $jour = $presence->getJour();
            $date = $jour->getDateJour();
            $std->date = $date ? $date->format('Y-m-d') : null;
            $std->date_fr = $date ? $this->dateService->getFr($date, true) : null;
            $data[] = $std;
        }

        return $data;
    }

    /**
     * @param Paiement[] $paiments
     *
     * @return Paiement[]
     */
    public function serializePaiement(iterable $paiments)
    {
        $data = [];
        foreach ($paiments as $paiment) {
            $std = new \stdClass();
            $std->id = $paiment->getId();
            $std->date = $paiment->getDatePaiement()->format('Y-m-d');
            $std->montant = $paiment->getMontant();
            $std->type = $paiment->getTypePaiement();
            $std->mode = $paiment->getModePaiement();
            $data[] = $std;
        }

        return $data;
    }

    /**
     * @param Plaine $plaine
     *
     * @return Plaine| \stdClass|null
     */
    public function serializePlaine(?Plaine $plaine)
    {
        if ($plaine) {
            $std = new \stdClass();
            $std->id = $plaine->getId();
            $std->intitule = $plaine->getIntitule();

            return $std;
        }

        return null;
    }

    /**
     * @param Ecole[] $ecoles
     *
     * @return Ecole[]
     */
    public function serializeEcole(array $ecoles)
    {
        $data = [];
        foreach ($ecoles as $ecole) {
            $std = new \stdClass();
            $std->id = $ecole->getId();
            $std->nom = $ecole->getNom();
            $data[] = $std;
        }

        return $data;
    }

    public function serializeAnnee(iterable $annees)
    {
        $data = [];
        $i = 1;
        foreach ($annees as $nom) {
            $std = new \stdClass();
            $std->id = $i;
            $std->nom = $nom;
            $data[] = $std;
            ++$i;
        }

        return $data;
    }

    /**
     * @return \stdClass
     */
    public function serializeUser(User $user)
    {
        $token = '123456';
        $std = new \stdClass();
        $std->id = $user->getId();
        $std->nom = $user->getNom();
        $std->prenom = $user->getPrenom();
        $std->email = $user->getEmail();
        $std->token = $token;

        $user->setToken($token);

        return $std;
    }

    /**
     * @param SanteFiche[] $santeFiches
     *
     * @return \stdClass[]
     */
    public function serializeSanteFiche(iterable $santeFiches)
    {
        $data = [];
        foreach ($santeFiches as $santeFiche) {
            $std = new \stdClass();
            $std->id = $santeFiche->getId();
            $std->medecinNom = $santeFiche->getMedecinNom();
            $std->medecinTelephone = $santeFiche->getMedecinTelephone();
            $std->personneUrgence = $santeFiche->getPersonneUrgence();
            $std->remarques = $santeFiche->getRemarques();
            $std->enfantId = $santeFiche->getEnfant()->getId();
            $data[] = $std;
        }

        return $data;
    }

    /**
     * @param SanteQuestion[] $questions
     *
     * @return \stdClass[]
     */
    public function serializeQuestion(iterable $questions)
    {
        $data = [];
        foreach ($questions as $question) {
            $std = new \stdClass();
            $std->id = $question->getId();
            $std->intitule = $question->getIntitule();
            $std->complement = $question->getComplement();
            $std->complement_label = $question->getComplementLabel();
            $data[] = $std;
        }

        return $data;
    }

    /**
     * @param SanteReponse[] $reponses
     *
     * @return \stdClass[]
     */
    public function serializeReponse(iterable $reponses)
    {
        $data = [];
        foreach ($reponses as $reponse) {
            $std = new \stdClass();
            $std->id = $reponse->getId();
            $std->santeFicheId = $reponse->getSanteFiche()->getId();
            $std->questionId = $reponse->getQuestion()->getId();
            $std->remarque = $reponse->getRemarque();
            $std->reponse = $reponse->getReponse() ? 'oui' : 'non';
            $data[] = $std;
        }

        return $data;
    }
}
