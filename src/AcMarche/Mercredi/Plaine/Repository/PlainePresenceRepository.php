<?php

namespace AcMarche\Mercredi\Plaine\Repository;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Plaine\Entity\PlaineEnfant;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PlainePresence|null   find($id, $lockMode = null, $lockVersion = null)
 * @method PlainePresence|null   findOneBy(array $criteria, array $orderBy = null)
 * @method PlainePresence[]|null findAll()
 * @method PlainePresence[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlainePresenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlainePresence::class);
    }

    public function insert(PlainePresence $plainePresence)
    {
        $this->persist($plainePresence);
        $this->_em->flush();
    }

    public function persist(PlainePresence $plainePresence)
    {
        $this->_em->persist($plainePresence);
    }

    public function save()
    {
        $this->_em->flush();
    }

    public function setCriteria($args)
    {
        $plaine_enfant_id = isset($args['plaine_enfant_id']) ? $args['plaine_enfant_id'] : 0;
        $jour_id = isset($args['jour_id']) ? $args['jour_id'] : 0;
        $id = isset($args['id']) ? $args['id'] : 0;
        $absent = isset($args['absent']) ? $args['absent'] : null;
        $plaine_enfant = isset($args['plaine_enfant']) ? $args['plaine_enfant'] : 0;
        $jour = isset($args['jour']) ? $args['jour'] : 0;
        $tuteur = isset($args['tuteur']) ? $args['tuteur'] : 0;
        $nonpaye = isset($args['nonpaye']) ? $args['nonpaye'] : 0;
        $date = isset($args['date']) ? $args['date'] : null;
        $onlyPaye = isset($args['onlypaye']) ? $args['onlypaye'] : null;
        $today = isset($args['today']) ? $args['today'] : null;

        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.jour', 'jour', 'WITH')
            ->leftJoin('p.plaine_enfant', 'plaineEnfant', 'WITH')
            ->leftJoin('p.paiement', 'paiement', 'WITH')
            ->leftJoin('plaineEnfant.enfant', 'enfant', 'WITH')
            ->leftJoin('p.tuteur', 'tuteur', 'WITH');

        $qb->addSelect('jour', 'paiement', 'plaineEnfant', 'enfant', 'tuteur');

        if ($id) {
            $qb->andwhere('p.id = :id')
                ->setParameter('id', $id);
        }

        if ($jour_id) {
            $qb->andwhere('p.jour = :jour')
                ->setParameter('jour', $jour_id);
        }

        if ($plaine_enfant_id) {
            $qb->andwhere('p.plaine_enfant = :plaine')
                ->setParameter('plaine', $plaine_enfant_id);
        }

        if ($jour) {
            $qb->andwhere('p.jour = :jour')
                ->setParameter('jour', $jour);
        }

        if ($plaine_enfant) {
            $qb->andwhere('p.plaine_enfant = :plaine')
                ->setParameter('plaine', $plaine_enfant);
        }

        if ($tuteur) {
            $qb->andwhere('p.tuteur = :tuteur')
                ->setParameter('tuteur', $tuteur);
        }

        //si absent vaut true, je ne prend PAS les presences absentes
        if ($absent) {
            $qb->andwhere('p.absent = :absent')
                ->setParameter('absent', 0);
        }

        if ($nonpaye) {
            $qb->andwhere('p.paiement IS NULL');
        }

        if ($date) {
            $qb->andwhere('jour.date_jour LIKE :date2')
                ->setParameter('date2', '%'.$date.'%');
        }

        if ($today) {
            $today = new \DateTime();
            $qb->andwhere('jour.date_jour <= :date')
                ->setParameter('date', $today);
        }

        if ($onlyPaye) {
            $qb->andwhere('p.paiement IS NOT NULL');
        }

        return $qb;
    }

    /**
     * Donne la liste des enfants inscrits a la plaine
     * Util quand j'ajoute une date a une plaine.
     *
     * @param int $plaine_id
     *
     * @return Enfant[]
     */
    public function getEnfantsByPlaineId($plaine_id = null)
    {
        if (!$plaine_id) {
            return [];
        }

        $qb = $this->createQueryBuilder('p');
        $qb->leftJoin('p.enfant', 'e', 'WITH');
        $qb->addSelect('e');

        $qb->andwhere('p.plaine = :plaine_id')
            ->setParameter('plaine_id', $plaine_id)
            ->groupBy('p.enfant')
            ->orderBy('p.enfant');

        $query = $qb->getQuery();

        //echo  $query->getSQL();

        $results = $query->getResult();

        $enfants = [];

        foreach ($results as $result) {
            $enfants[] = $result->getEnfant();
        }

        return $enfants;
    }

    /**
     * Donne la liste des enfants inscrits a la plaine
     * Util quand j'ajoute une date a une plaine.
     *
     * @param array $plaineEnfantIds
     * @param int   $plaine_id
     *
     * @return Enfant[]
     */
    public function getEnfantsByPlaineAndByJour($plaineEnfantIds, $jour_id)
    {
        if (!$jour_id) {
            return [];
        }

        $qb = $this->createQueryBuilder('pp');
        $qb->leftJoin('pp.plaine_enfant', 'pe', 'WITH');
        $qb->leftJoin('pe.enfant', 'e', 'WITH');
        $qb->addSelect('pe', 'e');

        $qb->andwhere('pp.jour = :jour')
            ->setParameter('jour', $jour_id);

        $enfants_string = implode("','", $plaineEnfantIds);

        $qb->andWhere('pp.plaine_enfant IN (\''.$enfants_string.'\')');

        $qb->orderBy('e.nom');

        $query = $qb->getQuery();

        $presences = $query->getResult();

        $enfants = [];

        foreach ($presences as $presence) {
            $plaine_enfant = $presence->getPlaineEnfant();
            $enfants[] = $plaine_enfant->getEnfant();
        }

        return $enfants;
    }

    /**
     * Retoure la liste des jour pour lequels
     * l'enfant est inscrit.
     *
     * @return Jour[]
     */
    public function getJoursInscrits(PlaineEnfant $plaine_enfant)
    {
        $plaine_enfant_id = $plaine_enfant->getId();
        $presences = $this->search(['plaine_enfant_id' => $plaine_enfant_id]);

        $jours_enfant = [];
        foreach ($presences as $presence) {
            $jours_enfant[] = $presence->getJour();
        }

        return $jours_enfant;
    }

    /**
     * @param $args
     *
     * @return PlainePresence[]|PlainePresence
     */
    public function search($args)
    {
        $qb = $this->setCriteria($args);
        $one = isset($args['one']) ? $args['one'] : false;

        $qb->orderBy('p.jour');

        $query = $qb->getQuery();

        if ($one) {
            return $query->getOneOrNullResult();
        }

        //echo  $query->getSQL();

        $results = $query->getResult();

        return $results;
    }

    /**
     * @param $args
     *
     * @return PlainePresence[]
     */
    public function getPresencesNonPayes($args)
    {
        $args['nonpaye'] = true;
        $qb = $this->setCriteria($args);

        $qb->andWhere('p.absent != 1'); //absent avec certificat

        $order = isset($args['order']) ? $args['order'] : null;
        if ($order) {
            if ($order) {
                $qb->addOrderBy('enfant.nom');
            }
        }

        $query = $qb->getQuery();

        //echo $query->getSQL();

        $results = $query->getResult();

        return $results;
    }

    /**
     * Utiliser pour form plainePresencePaiementType.
     *
     * @param $args
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getPresencesNonPayes2($args)
    {
        $qb = $this->setCriteria($args);
        $qb->orderBy('p.jour');

        return $qb;
    }

    /**
     * @param $args
     *
     * @return PlainePresence[]
     */
    public function getPresences($args)
    {
        $tuteur = isset($args['tuteur']) ? $args['tuteur'] : null;
        $enfant = isset($args['enfant']) ? $args['enfant'] : null;
        $date = isset($args['date']) ? $args['date'] : null;
        $onlypaye = isset($args['onlypaye']) ? $args['onlypaye'] : null;

        $results = [];

        if (!$enfant or !$tuteur) {
            return $results;
        }

        $args = ['enfant_id' => $enfant->getId()];
        $enfantPlaines = $this->getEntityManager()->getRepository(PlaineEnfant::class)->search($args);

        foreach ($enfantPlaines as $enfantPlaine) {
            $args = [];
            if ($date) {
                $args['date'] = $date;
            }
            if ($onlypaye) {
                $args['onlypaye'] = $onlypaye;
            }
            $args['plaine_enfant_id'] = $enfantPlaine->getId();
            $qb = $this->setCriteria($args);
            $qb->orderBy('p.jour');
            $query = $qb->getQuery();
            $tmp = $query->getResult();

            if (count($tmp) > 0) {
                $results = array_merge($results, $tmp);
            }
        }

        return $results;
    }
}
