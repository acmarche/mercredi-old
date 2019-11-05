<?php

namespace AcMarche\Mercredi\Admin\Service;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Entity\Paiement;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Plaine\Entity\PlaineEnfant;
use AcMarche\Mercredi\Plaine\Entity\PlaineJour;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Facture.
 */
class FacturePlaine
{
    private $facture;
    private $em;

    public function __construct(EntityManagerInterface $em, Facture $facture)
    {
        $this->facture = $facture;
        $this->em = $em;
    }

    /**
     * Traitement de la presence
     * Ajouter la fratrie presente le meme jour
     * Change l'ordre de l'enfant suivant la fratrie
     * Calcul le cout de la journee.
     *
     * @param [] $fratries
     */
    public function handlePresence(PlainePresence $presence, $fratries = [])
    {
        $presenceFratries = $this->getFratrieByPresence($presence, $fratries);

        $presence->addFratries($presenceFratries);
        $ordre = $this->getOrdre($presence, $presenceFratries);
        $presence->setOrdre($ordre);
        $presence->setOrdreNew($ordre);
        $prix = $this->getPrix($presence->getJour(), $ordre);
        $presence->setPrix($prix);
        $cout = $this->getCout($presence, $ordre);
        $presence->setCout($cout);
    }

    /**
     * Pour obtenir l'ordre, j'ai besoin de la fratrie ce jour la.
     *
     * @param array|null $fratries
     *
     * @return int
     */
    public function getOrdre(PlainePresence $presence, $fratries = null)
    {
        $plaineEnfant = $presence->getPlaineEnfant();
        $tuteur = $presence->getTuteur();
        $enfant = $plaineEnfant->getEnfant();

        /*
         * si ordre sur presence
         */
        if ($presence->getOrdre()) {
            return $presence->getOrdre();
        }

        $ordreBase = $enfant->getOrdre();

        /*
         * si ordre definit dans la relation tuteur enfant
         */
        if ($tuteur) {
            $args = ['enfant_id' => $enfant->getId(), 'tuteur_id' => $tuteur->getId(), 'one' => 1];
            $enfantTuteur = $this->em->getRepository(EnfantTuteur::class)->search($args);

            if ($enfantTuteur->getOrdre()) {
                $ordreBase = $enfantTuteur->getOrdre();
            }
        }

        /*
         * ordre change pas quand vaut 1
         */
        if (1 == $ordreBase) {
            return $ordreBase;
        }

        if (!$fratries) {
            $fratries = $this->getFratrieByPresence($presence, null, false);
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

    public function getPrix(PlaineJour $jour, $ordre)
    {
        $plaine = $jour->getPlaine();

        switch ($ordre) {
            case 1:
                $prix = $plaine->getPrix1();
                break;
            case 2:
                $prix = $plaine->getPrix2();
                break;
            default:
                $prix = $plaine->getPrix3();
        }

        return $prix;
    }

    /**
     * Retourne le prix suivant l'ordre.
     *
     * @param Presence $presence
     * @param int      $ordre
     *
     * @return int
     */
    public function getCout(PlainePresence $presence, $ordre)
    {
        $jour = $presence->getJour();
        $prix = $this->getPrix($jour, $ordre);

        return $prix;
    }

    /**
     * Retourne la liste des fratrie presente sur une presence
     * Je prends toute la fratrie de tous les tuteurs
     * puis je retire ceux qui ne sont pas presents.
     *
     * @param null $fratries
     * @param bool $withAbsent
     *
     * @return array
     */
    public function getFratrieByPresence(PlainePresence $presence, $fratries = null, $withAbsent = true)
    {
        $fratriesNew = [];

        $plaineEnfant = $presence->getPlaineEnfant();
        $tuteur = $presence->getTuteur();
        $enfant = $plaineEnfant->getEnfant();
        $plaine = $plaineEnfant->getPlaine();
        $jour = $presence->getJour();

        if (!$fratries) {
            $fratries = $fratries = $this->getFratrie($enfant);
        }

        /*
         * frere inscrit a la plaine ?
         */
        foreach ($fratries as $fratrie) {
            $args = ['plaine_id' => $plaine->getId(), 'enfant_id' => $fratrie->getId()];
            $present = $this->em->getRepository(PlaineEnfant::class)->search($args);

            if ($present) {
                $fratrieClone = clone $fratrie; //je clone sinon modifie objet pour toutes les presences

                $presenceFratrie = $this->em->getRepository(PlainePresence::class)->search(
                    [
                        'jour' => $jour,
                        'tuteur' => $tuteur,
                        'plaine_enfant' => $present,
                        'one' => true,
                    ]
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
        }

        return $fratriesNew;
    }

    /**
     * Retourne la fratrie.
     *
     * @param Tuteur $tuteur
     *
     * @return array(Object Enfant)
     */
    public function getFratrie(Enfant $enfant, Tuteur $tuteur = null)
    {
        $fratries = $this->em->getRepository(Enfant::class)->getFratriesBy(
            $enfant,
            $tuteur
        );

        return $fratries;
    }

    /**
     * Je vais chercher tous les enfants
     * Par parent :
     * - Je vais chercher les paiements
     *  Par paiement
     *      - Je vais chercher les presences du admin
     *      - Je vais chercher les presences des plaines
     *      Et j'effectue pour chaque presence un traitement
     * - Je vais chercher les presences non payes (admin et plaines).
     */
    public function traitement(Tuteur $tuteur)
    {
        $args = ['tuteur_id' => $tuteur->getId()];

        /**
         * je parcours tous les enfants du tuteur.
         */
        $tuteurEnfants = $this->em->getRepository(EnfantTuteur::class)->search($args);

        foreach ($tuteurEnfants as $tuteurEnfant) {
            $enfant = $tuteurEnfant->getEnfant();
            $paiements = $this->em->getRepository(Paiement::class)->getByEnfantTuteur($tuteurEnfant);
            foreach ($paiements as $paiement) {
                $presences = $paiement->getPresences();
                $plainePresences = $paiement->getPlainePresences();
                foreach ($presences as $presence) {
                    $this->facture->handlePresence($presence);
                }

                foreach ($plainePresences as $plainePresence) {
                    $this->handlePresence($plainePresence);
                }
            }
            $tuteurEnfant->addPaiements($paiements);

            $this->getNonPayes($tuteur, $enfant, $tuteurEnfant);
        }

        return $tuteurEnfants;
    }

    /**
     * @param Tuteur       $tuteur
     * @param Enfant       $enfant
     * @param EnfantTuteur $tuteurEnfant
     * @param Facture      $facture
     */
    protected function getNonPayes($tuteur, $enfant, $tuteurEnfant)
    {
        $args2 = ['enfant_id' => $enfant->getId(), 'tuteur_id' => $tuteur->getId(), 'result' => true];
        $nonpayes = $this->em->getRepository(Presence::class)->getPresencesNonPayes($args2);
        foreach ($nonpayes as $nonpaye) {
            $this->facture->handlePresence($nonpaye);
        }

        /**
         * plaines non payes.
         */
        $plainesnonpayes = [];

        $plaines = $this->em->getRepository(PlaineEnfant::class)->search(
            ['enfant_id' => $enfant->getId()]
        );
        foreach ($plaines as $plaine) {
            $args3 = ['tuteur' => $tuteur, 'plaine_enfant' => $plaine];
            $tmp = $this->em->getRepository(PlainePresence::class)->getPresencesNonPayes($args3);
            foreach ($tmp as $presenceTmp) {
                $plainesnonpayes[] = $presenceTmp;
                $this->handlePresence($presenceTmp);
            }
        }

        $tuteurEnfant->addPlainePresencesNonPayes($plainesnonpayes);
        $tuteurEnfant->addPresencesNonPayes($nonpayes);
    }
}
