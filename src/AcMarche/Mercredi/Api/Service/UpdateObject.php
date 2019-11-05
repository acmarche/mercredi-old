<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 22/01/19
 * Time: 11:08.
 */

namespace AcMarche\Mercredi\Api\Service;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Repository\EcoleRepository;
use AcMarche\Mercredi\Admin\Repository\JourRepository;
use AcMarche\Mercredi\Admin\Service\PresenceService;
use AcMarche\Mercredi\Security\Entity\User;

class UpdateObject
{
    /**
     * @var EcoleRepository
     */
    private $ecoleRepository;
    /**
     * @var PresenceService
     */
    private $presenceService;
    /**
     * @var JourRepository
     */
    private $jourRepository;

    public function __construct(
        EcoleRepository $ecoleRepository,
        PresenceService $presenceService,
        JourRepository $jourRepository
    ) {
        $this->ecoleRepository = $ecoleRepository;
        $this->presenceService = $presenceService;
        $this->jourRepository = $jourRepository;
    }

    public function updateTuteur(Tuteur $tuteur, object $data)
    {
        $tuteur->setNom($data->nom);
        $tuteur->setPrenom($data->prenom);
        $tuteur->setAdresse($data->adresse);
        $tuteur->setLocalite($data->localite);
        $tuteur->setCodePostal($data->code_postal);
        $tuteur->setEmail($data->email);
        $tuteur->setTelephone($data->code_postal);
    }

    public function updateEnfant(Enfant $enfant, object $data)
    {
        $enfant->setNom($data->nom);
        $enfant->setPrenom($data->prenom);
        $enfant->setNumeroNational($data->numero_national);
        $enfant->setSexe($data->sexe);
        $ecole = $this->ecoleRepository->find($data->ecole_id);
        if ($ecole) {
            $enfant->setEcole($ecole);
        }
        $enfant->setAnneeScolaire($data->annee_scolaire);
        $enfant->setRemarques($data->remarques);
    }

    /**
     * @param Tuteur   $tuteur
     * @param iterable $presences
     */
    public function insertPresences(User $user, Enfant $enfant, iterable $joursIds)
    {
        $tuteur = $user->getTuteur();

        $jours = $this->jourRepository->findBy(['id' => $joursIds]);

        $presence = $this->presenceService->initPresence($enfant, $user);

        $this->presenceService->addPresences($presence, $tuteur, $jours);
    }
}
