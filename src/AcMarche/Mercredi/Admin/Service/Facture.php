<?php

namespace AcMarche\Mercredi\Admin\Service;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Repository\EnfantTuteurRepository;
use AcMarche\Mercredi\Admin\Repository\PresenceRepository;
use AcMarche\Mercredi\Commun\Utils\SortUtils;

/**
 * Facture
 *
 */
class Facture
{
    /**
     * @var FraterieService
     */
    private $fraterieService;
    /**
     * @var EnfantTuteurRepository
     */
    private $enfantTuteurRepository;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var SortUtils
     */
    private $sortUtils;
    /**
     * @var OrdreService
     */
    private $ordreService;

    public function __construct(
        FraterieService $fraterieService,
        EnfantTuteurRepository $enfantTuteurRepository,
        PresenceRepository $presenceRepository,
        SortUtils $sortUtils,
        OrdreService $ordreService
    ) {
        $this->fraterieService = $fraterieService;
        $this->enfantTuteurRepository = $enfantTuteurRepository;
        $this->presenceRepository = $presenceRepository;
        $this->sortUtils = $sortUtils;
        $this->ordreService = $ordreService;
    }

    public function getPrix(Jour $jour, $ordre)
    {
        switch ($ordre) {
            case 1:
                $prix = $jour->getPrix1();
                break;
            case 2:
                $prix = $jour->getPrix2();
                break;
            default:
                $prix = $jour->getPrix3();
        }

        return $prix;
    }

    public function getPrixTmp(Jour $jour, $ordre)
    {
        switch ($ordre) {
            case 1:
                $prix = 4.95;
                break;
            case 2:
                $prix = 3.15;
                break;
            default:
                $prix = 2.25;
        }

        return $prix;
    }

    /**
     * Retourne le prix moins la reduction
     * @param Presence $presence
     * @param int $ordre
     * @return int
     */
    public function getCout(Presence $presence, $ordre)
    {
        $reduction = $presence->getReduction();
        $pourcentage = 0;
        if ($reduction) {
            $pourcentage = $reduction->getPourcentage();
            if ($pourcentage == 100) { //cpas ou - 15 minutes
                return 0;
            }
        }

        if ($presence->getAbsent() == 1) {
            return 0;
        }

        $jour = $presence->getJour();
        //@todo $presence->getPrix()
        $prix = $this->getPrix($jour, $ordre);

        if ($pourcentage > 0) {
            return $prix - (($prix / 100) * $pourcentage);
        }

      /*  if ($paiement = $presence->getPaiement()) {
            if ($paiement->getTypePaiement() === 'Abonnement') {
                return $paiement->getMontant() / 5;
            }
        }*/

        return $prix;
    }

    public function getCoutTmp(Presence $presence, $ordre)
    {
        $reduction = $presence->getReduction();
        $pourcentage = 0;
        if ($reduction) {
            $pourcentage = $reduction->getPourcentage();
            if ($pourcentage == 100) { //cpas ou - 15 minutes
                return 0;
            }
        }

        if ($presence->getAbsent() == 1) {
            return 0;
        }

        $jour = $presence->getJour();
        $prix = $this->getPrixTmp($jour, $ordre);

        if ($pourcentage > 0) {
            return $prix - (($prix / 100) * $pourcentage);
        }

        return $prix;
    }

    /**
     * Va chercher toutes les presences
     * Je vais chercher les parents
     * Pour chaque parent je vais chercher :
     * - les presences
     * - la fratrie
     * Et j'effectue pour chaque presence un traitement
     *
     * @return array EnfantTuteur
     *
     */
    public function traitement(Enfant $enfant)
    {
        $args = array('enfant_id' => $enfant->getId());
        /**
         * je vais chercher tous les parents
         * pas entity->getTuteurs() car j'utilise les jointures
         */

        $enfant_tuteurs = $this->enfantTuteurRepository->search(
            $args
        );

        foreach ($enfant_tuteurs as $enfantTuteur) {
            $tuteur = $enfantTuteur->getTuteur();

            $presences = $this->presenceRepository->getByEnfantTuteur($enfantTuteur);
            $fratries = $this->fraterieService->getFratrie($enfant, $tuteur);

            foreach ($presences as $presence) {
                $this->handlePresence($presence, $fratries);
                $enfantTuteur->addPresence($presence);
            }

            $presences2 = $this->sortUtils->sortPresence($enfantTuteur);
            $enfantTuteur->addPresencesByMonth($presences2);
        }

        return $enfant_tuteurs;
    }

    /**
     * Traitement de la presence
     * Determine l'ordre
     * Determine la fratrie presente
     * Determine le prix suivant l'ordre
     * Determine le cout apres reduction
     * @param Presence $presence
     * @param array $fratries
     */
    public function handlePresence(Presence $presence, $fratries = array())
    {
        $presenceFratries = $this->fraterieService->getFratrieByPresence($presence, $fratries);
        $presence->addFratries($presenceFratries);
        $ordre = $this->ordreService->getOrdre($presence, $presenceFratries);
        $presence->setOrdreNew($ordre);
        $prix = $this->getPrix($presence->getJour(), $ordre);
        $presence->setPrix($prix);
        $cout = $this->getCout($presence, $ordre);
        $presence->setCout($cout);
    }

    /**
     * Traitement de la presence
     * Determine l'ordre
     * Determine la fratrie presente
     * Determine le prix suivant l'ordre
     * Determine le cout apres reduction
     * @param Presence $presence
     * @param array $fratries
     */
    public function handleTmpPresence(Presence $presence, $fratries = array())
    {
        $presenceFratries = $this->fraterieService->getFratrieByPresence($presence, $fratries);
        $presence->addFratries($presenceFratries);
        $ordre = $this->ordreService->getOrdre($presence, $presenceFratries);
        $presence->setOrdreNew($ordre);

        $prix = $this->getPrix($presence->getJour(), $ordre);
        $presence->setPrix($prix);
        $prixTmp = $this->getPrixTmp($presence->getJour(), $ordre);
        $presence->setPrixTmp($prixTmp);

        $cout = $this->getCout($presence, $ordre);
        $presence->setCout($cout);
        $coutTmp = $this->getCoutTmp($presence, $ordre);
        $presence->setCoutTmp($coutTmp);
    }


}
