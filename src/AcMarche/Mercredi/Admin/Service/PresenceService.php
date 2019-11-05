<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 9/01/18
 * Time: 13:16.
 */

namespace AcMarche\Mercredi\Admin\Service;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Repository\EnfantRepository;
use AcMarche\Mercredi\Admin\Repository\EnfantTuteurRepository;
use AcMarche\Mercredi\Admin\Repository\JourRepository;
use AcMarche\Mercredi\Admin\Repository\PresenceRepository;
use AcMarche\Mercredi\Commun\Utils\SortUtils;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use AcMarche\Mercredi\Plaine\Repository\PlaineJourRepository;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Security\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

class PresenceService
{
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var JourRepository
     */
    private $jourRepository;
    /**
     * @var PlaineJourRepository
     */
    private $plaineJourRepository;
    /**
     * @var PlainePresenceRepository
     */
    private $plainePresenceRepository;
    /**
     * @var SortUtils
     */
    private $sortUtils;
    /**
     * @var EnfantTuteurRepository
     */
    private $enfantTuteurRepository;
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;

    public function __construct(
        PresenceRepository $presenceRepository,
        JourRepository $jourRepository,
        PlaineJourRepository $plaineJourRepository,
        PlainePresenceRepository $plainePresenceRepository,
        EnfantTuteurRepository $enfantTuteurRepository,
        EnfantRepository $enfantRepository,
        SortUtils $sortUtils
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->jourRepository = $jourRepository;
        $this->plaineJourRepository = $plaineJourRepository;
        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->sortUtils = $sortUtils;
        $this->enfantTuteurRepository = $enfantTuteurRepository;
        $this->enfantRepository = $enfantRepository;
    }

    public function initPresence(Enfant $enfant, User $user)
    {
        $presence = new Presence();
        $presence->setEnfant($enfant);
        $presence->setUserAdd($user);

        return $presence;
    }

    public function addPresences(Presence $presenceInit, Tuteur $tuteur, $jours)
    {
        foreach ($jours as $jour) {
            if ($this->presenceExist($presenceInit->getEnfant(), $jour)) {
                continue;
            }

            $presence = clone $presenceInit;
            if ($tuteur) {
                $presence->setTuteur($tuteur);
            }
            $presence->setJour($jour);

            $this->presenceRepository->insert($presence);
        }
    }

    public function getPresences(Enfant $enfant, Tuteur $tuteur)
    {
        return $this->presenceRepository->findBy(
            [
                'enfant' => $enfant,
                'tuteur' => $tuteur,
            ]
        );
    }

    public function presenceExist(Enfant $enfant, Jour $jour)
    {
        return $this->presenceRepository->findOneBy(
            [
                'enfant' => $enfant,
                'jour' => $jour,
            ]
        );
    }

    /**
     * @param string $moisAnnee 03/12
     * @param $type 1 mercredi et plaine, 2 mercredi, 3 plaine
     *
     * @return array
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPresencesAndEnfantsByMonth($moisAnnee, $type)
    {
        $allenfants = [];

        $args_mois = ['date' => $moisAnnee];

        if (1 == $type or 2 == $type) {
            /**
             * j'obtiens les jours du mercredi du mois.
             */
            $jours = $this->jourRepository->search($args_mois);

            /*
             * pour chaque date, je vais chercher les presences
             */
            foreach ($jours as $jour) {
                $args = ['jour_id' => $jour->getId(), 'absent' => 1];
                $enfants = [];
                $date = $jour->getDateJour()->format('d-m-Y');
                $presences = $this->presenceRepository->search($args);
                $count = count($presences);
                foreach ($presences as $presence) {
                    $enfant = $presence->getEnfant();
                    $enfants[] = $enfant;
                    if (!in_array($enfant, $allenfants)) {
                        $allenfants[] = $enfant;
                    }
                }
                $allpresences[$date] = [
                    'count' => $count,
                    'enfants' => $enfants,
                ];
            }
        }

        if (1 == $type or 3 == $type) {
            /**
             * j'obtiens les jours de la plaine du mois.
             */
            $plaines_jours = $this->plaineJourRepository->search($args_mois);
            /*
             * pour chaque date, je vais chercher les presences
             */
            foreach ($plaines_jours as $jour) {
                $args = ['jour_id' => $jour->getId(), 'absent' => 1];
                $enfants = [];
                $date = $jour->getDateJour()->format('d-m-Y');
                $presences = $this->plainePresenceRepository->search($args);
                $count = count($presences);
                foreach ($presences as $presence) {
                    $plaine_enfant = $presence->getPlaineEnfant();
                    $enfant = $plaine_enfant->getEnfant();
                    $enfants[] = $enfant;
                    if (!in_array($enfant, $allenfants)) {
                        $allenfants[] = $enfant;
                    }
                }
                $allpresences[$date] = [
                    'count' => $count,
                    'enfants' => $enfants,
                ];
            }
        }

        /**
         * je trie le tableau des dates.
         */
        $allpresences = $this->sortUtils->sortByDateTime($allpresences);

        /**
         * Je trie le tableau enfants.
         */
        $allenfants = $this->sortUtils->sortObjectsByName($allenfants);

        return ['allenfants' => $allenfants, 'allpresences' => $allpresences];
    }

    /**
     * @param Presence[]|PlainePresence[] $presences
     *
     * @return array
     */
    public function getTuteursByPrences($presences)
    {
        $tuteurs = new ArrayCollection();
        foreach ($presences as $presence) {
            $tuteur = $presence->getTuteur();
            if ($tuteur && !$tuteurs->contains($tuteur)) {
                $tuteurs->add($tuteur);
            }
        }

        return $tuteurs->toArray();
    }

    /**
     * Calcul le cout d'une journée en prenant en compte tous les parametres.
     *
     * @param string $type admin ou plaine
     *
     * @return array(
     *                'ordre', //apres test provenance
     *                'ordre_provenance', //fiche,parente,presence
     *                'prix', //prix plein suivant l'ordre
     *                'pourcentage', //si reduction
     *                'fratries', //ce jour la
     *                'absence',   //non, oui avec certfi ou pas
     *                'montant', //cout apres tous les calculs
     *                )
     */
    public function calculCout(Presence $presence, Enfant $enfant)
    {
        $cout = [];
        $ordre_fiche = $enfant->getOrdre();

        $tuteur = $presence->getTuteur();
        $enfant_tuteur = $this->enfantTuteurRepository->findOneBy(
            ['enfant' => $enfant, 'tuteur' => $tuteur]
        );

        /**
         * Ordre de l'enfant par importance decroissante.
         */
        $ordre = false;
        //sur la presence elle meme
        if ($presence->getOrdre()) {
            $ordre = $presence->getOrdre();
            $cout['ordre_provenance'] = 'Sur la présence elle même';
        }

        if (!$ordre) {
            //sur la relation parent enfant
            $cout['ordre_provenance'] = 'Relation parent enfant';
            $ordre = $enfant_tuteur->getOrdre() ? $enfant_tuteur->getOrdre() : false;
        }

        if (!$ordre) {
            //sur la fiche de l'enfant
            $cout['ordre_provenance'] = 'Sur la fiche enfant';
            $ordre = $ordre_fiche ? $ordre_fiche : false;
        }

        $cout['ordre'] = $ordre;

        /**
         * Fratries.
         */
        $fratries = $this->enfantRepository->getFratries(
            $enfant->getId()
        );

        //fratrie presente ce jour la
        //fait $presence->addFratrie();
        $this->presenceRepository->setFratriesByPresence($presence, $fratries);

        $fratries_presence = $presence->getFratries();

        $fratries_count = count($fratries_presence);
        //si ordre enfant = 1, peu importe
        //si ordre enfant = 2, doit avoir 1 fratrie
        //si ordre enfant = 3, doit avoir 2 fratries
        //si ordre enfant = 4, doit avoir 3 fratries
        /*  if ($ordre_fiche > 1) {
              if (($ordre_fiche - 1) == $fratries_count) {
                  //var_dump("ok");
              } else {
                  //var_dump("ko");
              }
          }*/

        $cout['fratries'] = $fratries_presence;

        /**
         * Prix hors reduction.
         */
        $jour = $presence->getJour();
        $prix = $jour->getPrixByOrdre($ordre);

        $cout['prix'] = $prix;
        $cout['montant'] = $prix;

        /**
         * Reduction.
         */
        $reduction = $presence->getReduction();
        $cout['pourcentage'] = 0;
        if ($reduction) {
            $pourcentage = $reduction->getPourcentage();
            $cout['pourcentage'] = '- '.$prix - (($prix / 100) * $pourcentage).' euros';
            $cout['montant'] = $prix - (($prix / 100) * $pourcentage);
        }

        /**
         * Paiement.
         */
        $paiement = $presence->getPaiement();
        if ($paiement) {
            $paiement_type = $paiement->getTypePaiement();

            if ('abonnement' == $paiement_type) {
                //?
            }
        }

        /**
         * Absence.
         */
        $absent = $presence->getAbsent();

        //absent avec certificat cout passe a zero
        $cout['absence'] = 'Non';

        if ($absent > 0) {
            $cout['absence'] = 'Oui';
            $cout['montant'] = 0;
        }

        return $cout;
    }
}
