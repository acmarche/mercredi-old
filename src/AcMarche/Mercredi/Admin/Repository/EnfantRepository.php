<?php

namespace AcMarche\Mercredi\Admin\Repository;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Enfant|null   find($id, $lockMode = null, $lockVersion = null)
 * @method Enfant|null   findOneBy(array $criteria, array $orderBy = null)
 * @method Enfant[]|null findAll()
 * @method Enfant[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnfantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enfant::class);
    }

    public function insert(Enfant $enfant)
    {
        $this->_em->persist($enfant);
        $this->save();
    }

    public function remove(Enfant $enfant)
    {
        $this->_em->remove($enfant);
        $this->save();
    }

    public function save()
    {
        $this->_em->flush();
    }

    /**
     * @param $nom
     *
     * @return Enfant[]
     */
    public function findForAutoComplete($nom)
    {
        $qb = $this->createQueryBuilder('enfant');

        $qb->andwhere(
            'enfant.nom LIKE :nom OR enfant.prenom LIKE :nom'
        )
            ->setParameter('nom', '%'.$nom.'%');

        $qb->orderBy('enfant.nom');

        $query = $qb->getQuery();

        $results = $query->getResult();

        return $results;
    }

    /**
     * Recherche d'un enfant suivant son nom
     * ou pas
     * Recuper sa liste de presence
     * et ses enfant_tuteurs.
     *
     * @return Enfant[] | Enfant
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function search(array $args)
    {
        $nom = isset($args['nom']) ? $args['nom'] : null;
        $ecole = isset($args['ecole']) ? $args['ecole'] : null;
        $ecoles = isset($args['ecoles']) ? $args['ecoles'] : null;
        $archive = isset($args['archive']) ? $args['archive'] : false;
        $enfant = isset($args['enfant']) ? $args['enfant'] : false;
        $one = isset($args['one']) ? $args['one'] : false;
        $slugname = isset($args['slugname']) ? $args['slugname'] : null;
        $jour = isset($args['jour']) ? $args['jour'] : null;

        $annee_scolaire = isset($args['annee_scolaire']) ? $args['annee_scolaire'] : '';

        $qb = $this->createQueryBuilder('enfant');
        $qb->leftJoin('enfant.tuteurs', 'tuteurs', 'WITH');
        $qb->leftJoin('enfant.presences', 'presences', 'WITH');
        $qb->leftJoin('enfant.notes', 'notes', 'WITH');
        $qb->leftJoin('enfant.paiements', 'paiements', 'WITH');
        $qb->addSelect('tuteurs', 'presences', 'notes', 'paiements');

        if ($jour) {
            $qb->andWhere('presences.jour = :jour')
                ->setParameter('jour', $jour);
        }

        if ($nom) {
            $qb->andwhere('enfant.nom LIKE :nom OR enfant.prenom LIKE :nom')
                ->setParameter('nom', '%'.$nom.'%');
        }

        if ($slugname) {
            $qb->andwhere('enfant.slugname = :slugname')
                ->setParameter('slugname', $slugname);
        }

        if ($ecole) {
            $qb->andwhere('enfant.ecole = :ecole')
                ->setParameter('ecole', $ecole);
        }

        if ($ecoles) {
            $ids = $ecoles->map(
                function ($obj) {
                    return $obj->getId();
                }
            );
            $ids = join(',', $ids->toArray());

            $qb->andwhere('enfant.ecole IN (:ecoles)')
                ->setParameter('ecoles', "$ids");
        }

        if ($annee_scolaire) {
            $qb->andwhere('enfant.annee_scolaire LIKE :as')
                ->setParameter('as', '%'.$annee_scolaire.'%');
        }

        switch ($archive) {
            case 1:
                $qb->andwhere('enfant.archive = 1');
                break;
            case 2:
                break;
            default:
                $qb->andwhere('enfant.archive != 1');
                break;
        }

        if ($enfant) {
            $qb->andwhere('enfant = :id')
                ->setParameter('id', $enfant);
        }

        $qb->orderBy('enfant.nom');

        $query = $qb->getQuery();

        //echo  $query->getSQL();
        if ($one) {
            return $query->getOneOrNullResult();
        }

        $results = $query->getResult();

        return $results;
    }

    /**
     * @return Enfant[]
     */
    public function quickSearchActif(array $args)
    {
        $nom = isset($args['nom']) ? $args['nom'] : null;
        $ecole = isset($args['ecole']) ? $args['ecole'] : null;
        $annee_scolaire = isset($args['annee_scolaire']) ? $args['annee_scolaire'] : null;

        $qb = $this->createQueryBuilder('enfant');
        $qb->leftJoin('enfant.tuteurs', 'tuteurs', 'WITH');
        $qb->leftJoin('enfant.presences', 'presences', 'WITH');
        $qb->addSelect('tuteurs', 'presences');

        if ($nom) {
            $qb->andwhere('enfant.nom LIKE :nom OR enfant.prenom LIKE :nom')
                ->setParameter('nom', '%'.$nom.'%');
        }

        if ($ecole) {
            $qb->andwhere('enfant.ecole = :ecole')
                ->setParameter('ecole', $ecole);
        }

        if ($annee_scolaire) {
            $qb->andwhere('enfant.annee_scolaire LIKE :as')
                ->setParameter('as', '%'.$annee_scolaire.'%');
        }

        $qb->andwhere('enfant.archive != 1');
        $qb->orderBy('enfant.nom');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * Pour formulaire avec liste deroulante.
     *
     * @return QueryBuilder
     */
    public function getForList()
    {
        $qb = $this->createQueryBuilder('enfant');
        $qb->orderBy('enfant.nom, enfant.prenom');

        return $qb;
    }

    /**
     * Retourne un tableau d'objet Enfant.
     *
     * @param int $enfant_id
     * @param EnfantTuteur []
     *
     * @return Enfant[]|Enfant
     */
    public function getFratries($enfant_id, $enfant_tuteurs = [])
    {
        $em = $this->getEntityManager();

        //get id parents of enfant
        if (0 == count($enfant_tuteurs)) {
            $enfant_tuteurs = $em->getRepository(EnfantTuteur::class)->search(['enfant_id' => $enfant_id]);
        }

        $tuteurs_id = [];

        foreach ($enfant_tuteurs as $enfant_tuteur) {
            $tuteur = $enfant_tuteur->getTuteur();
            $tuteurs_id[] = $tuteur->getId();
        }

        $fratries = [];

        if (count($tuteurs_id) > 0) {
            /**
             * je vais chercher tous les enfants des parents.
             */
            $enfant_tuteurs2 = $em->getRepository(EnfantTuteur::class)->search(['tuteur_id' => $tuteurs_id]);

            foreach ($enfant_tuteurs2 as $enfant_tuteur) {
                $child = $enfant_tuteur->getEnfant();
                if ($enfant_id != $child->getId()) {
                    $fratries[] = $child;
                }
            }
        }

        return $fratries;
    }

    /**
     * Retourne la fratrie
     * Si pas de tuteur donner, va chercher tous les parents
     * Si tuteur ne donne que la fratrie de celui ci.
     *
     * @param Tuteur $tuteur
     *
     * @return Enfant[]
     */
    public function getFratriesBy(Enfant $enfant, Tuteur $tuteur = null)
    {
        $em = $this->getEntityManager();

        $fratries = [];
        $tuteurs = [];
        if ($tuteur) {
            $tuteurs = [$tuteur->getId()];
        } else {
            $parents = $em->getRepository(EnfantTuteur::class)->search(['enfant_id' => $enfant->getId()]);
            foreach ($parents as $parent) {
                $tuteurs[] = $parent->getTuteur()->getId();
            }
        }

        if (0 == count($tuteurs)) {
            return [];
        }

        $enfant_tuteurs = $em->getRepository(EnfantTuteur::class)->search(['tuteur_id' => $tuteurs]);

        foreach ($enfant_tuteurs as $enfant_tuteur) {
            $fratrie = $enfant_tuteur->getEnfant();
            if ($enfant !== $fratrie && !in_array($fratrie, $fratries)) {
                $fratries[] = $fratrie;
            }
        }

        return $fratries;
    }

    /**
     * Retourne la liste des enfants non inscrits a la plaine.
     *
     * @param Plaine $plaine
     *
     * @return QueryBuilder
     */
    public function getListEnfantsNonInscrits(Plaine $plaine = null)
    {
        /*
         * SELECT * FROM `admin`.`enfant` LEFT JOIN (
         *     SELECT * FROM plaine_enfant WHERE plaine_id = 5 ) ef
         *     ON enfant.id = ef.enfant_id
         * WHERE ef.plaine_id IS NULL ORDER BY `enfant`.`nom` ASC
         *  */

        $qb = $this->createQueryBuilder('enfant');
        $qb->leftJoin('enfant.plaines', 'enfantPlaines', 'WITH');
        $qb->addSelect('enfantPlaines', 'enfant');

        /*
         * je retire les enfants
         * deja inscrits
         */
        if ($plaine) {
            $qb->andwhere('enfantPlaines.plaine != :plaine OR enfantPlaines.plaine IS NULL')
                ->setParameter('plaine', $plaine->getId());
        }

        $qb->orderBy('enfant.nom');

        return $qb;
    }

    /**
     * @return Enfant[]
     */
    public function searchForEcole(array $args)
    {
        $nom = isset($args['nom']) ? $args['nom'] : null;
        $ecole = isset($args['ecole']) ? $args['ecole'] : null;
        $jour = isset($args['jour']) ? $args['jour'] : null;

        $qb = $this->createQueryBuilder('enfant');
        $qb->leftJoin('enfant.tuteurs', 'tuteurs', 'WITH');
        $qb->leftJoin('enfant.presences', 'presences', 'WITH');
        $qb->addSelect('tuteurs', 'presences');

        if ($jour) {
            $qb->andWhere('presences.jour = :jour')
                ->setParameter('jour', $jour);
        }

        if ($nom) {
            $qb->andwhere('enfant.nom LIKE :nom OR enfant.prenom LIKE :nom')
                ->setParameter('nom', '%'.$nom.'%');
        }

        if ($ecole) {
            $qb->andwhere('enfant.ecole = :ecole')
                ->setParameter('ecole', $ecole);
        }

        $qb->andwhere('enfant.archive = 0');

        $qb->orderBy('enfant.nom');

        $query = $qb->getQuery();

        return $query->getResult();
    }
}
