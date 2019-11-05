<?php

namespace AcMarche\Mercredi\Admin\Repository;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Presence|null   find($id, $lockMode = null, $lockVersion = null)
 * @method Presence|null   findOneBy(array $criteria, array $orderBy = null)
 * @method Presence[]|null findAll()
 * @method Presence[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PresenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Presence::class);
    }

    public function insert(Presence $presence)
    {
        $this->_em->persist($presence);
        $this->_em->flush();
    }

    public function save()
    {
        $this->_em->flush();
    }

    /**
     * Retour les presences d'un enfant suivant son tuteur.
     *
     * @param null $date
     * @param bool $onlyPaye
     *
     * @return Presence|Presence[]
     */
    public function getByEnfantTuteur(EnfantTuteur $enfant_tuteur, $date = null, $onlyPaye = false)
    {
        $enfant_id = $enfant_tuteur->getEnfant()->getId();
        $tuteur_id = $enfant_tuteur->getTuteur()->getId();

        $args = ['enfant_id' => $enfant_id, 'tuteur_id' => $tuteur_id];

        if ($date) {
            $args['date'] = $date;
        }
        if ($onlyPaye) {
            $args['onlypaye'] = true;
        }

        try {
            $presences = $this->search($args);
        } catch (NonUniqueResultException $e) {
            $presences = [];
        }

        return $presences;
    }

    /**
     * @param iterable|Tuteur[] $tuteurs
     *
     * @return mixed
     */
    public function getByTuteurs(Enfant $enfant, iterable $tuteurs, \DateTime $depuisLe)
    {
        $qb = $this->createQueryBuilder('presence');

        $qb->leftJoin('presence.jour', 'jour', 'WITH');
        $qb->leftJoin('presence.enfant', 'enfant', 'WITH');
        $qb->leftJoin('presence.tuteur', 'tuteur', 'WITH');
        $qb->addSelect('jour', 'tuteur', 'enfant');

        if ($tuteurs) {
            $qb->andwhere('tuteur IN (:tuteur)')
                ->setParameter('tuteur', $tuteurs);
        }

        if ($enfant) {
            $qb->andwhere('enfant = :enfant')
                ->setParameter('enfant', $enfant);
        }

        if ($depuisLe) {
            $qb->andwhere('jour.date_jour >= :date')
                ->setParameter('date', $depuisLe);
        }

        $qb->orderBy('jour.date_jour', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $args
     *
     * @return Presence[]|Presence
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function search($args = [])
    {
        $enfant_id = isset($args['enfant_id']) ? $args['enfant_id'] : null;
        $tuteur_id = isset($args['tuteur_id']) ? $args['tuteur_id'] : null;
        $jour_id = isset($args['jour_id']) ? $args['jour_id'] : null;
        $ordre = isset($args['order']) ? $args['order'] : null;
        $date = isset($args['date']) ? $args['date'] : null;
        $ecole = isset($args['ecole']) ? $args['ecole'] : null;
        $absent = isset($args['absent']) ? $args['absent'] : null;
        $id = isset($args['id']) ? $args['id'] : null;
        //new
        $enfant = isset($args['enfant']) ? $args['enfant'] : null;
        $tuteur = isset($args['tuteur']) ? $args['tuteur'] : null;
        $jour = isset($args['jour']) ? $args['jour'] : null;
        $one = isset($args['one']) ? $args['one'] : false;
        $enfantIn = isset($args['enfantin']) ? $args['enfantin'] : false;
        $user = isset($args['user']) ? $args['user'] : false;
        $onlyPaye = isset($args['onlypaye']) ? $args['onlypaye'] : false;
        $onlyNonPaye = isset($args['onlynonpaye']) ? $args['onlynonpaye'] : false;

        $qb = $this->createQueryBuilder('p');

        $qb->leftJoin('p.jour', 'j', 'WITH');
        $qb->leftJoin('p.enfant', 'e', 'WITH');
        $qb->leftJoin('p.tuteur', 't', 'WITH');
        $qb->leftJoin('p.reduction', 'r', 'WITH');
        $qb->leftJoin('p.paiement', 'pa', 'WITH');
        $qb->addSelect('j', 'e', 't', 'r', 'pa');

        if ($id) {
            $qb->andwhere('p.id = :id')
                ->setParameter('id', $id);
        }

        if ($enfant_id) {
            $qb->andwhere('p.enfant = :enfant')
                ->setParameter('enfant', $enfant_id);
        }

        if ($tuteur_id) {
            $qb->andwhere('p.tuteur = :tuteur')
                ->setParameter('tuteur', $tuteur_id);
        }

        if ($jour_id) {
            $qb->andwhere('p.jour = :jour')
                ->setParameter('jour', $jour_id);
        }

        if ($date) {
            $qb->andwhere('j.date_jour LIKE :date')
                ->setParameter('date', '%'.$date.'%');
        }

        if ($ecole) {
            $qb->andwhere('e.ecole = :ecole')
                ->setParameter('ecole', $ecole);
        }

        if ($enfant) {
            $qb->andwhere('p.enfant = :enfant')
                ->setParameter('enfant', $enfant);
        }

        if (is_array($enfantIn)) {
            $qb->andwhere('p.enfant IN :enfant')
                ->setParameter('enfant', '('.join(',', $enfantIn).')');
        }

        if ($tuteur) {
            $qb->andwhere('p.tuteur = :tuteur')
                ->setParameter('tuteur', $tuteur);
        }

        if ($jour) {
            $qb->andwhere('p.jour = :jour')
                ->setParameter('jour', $jour);
        }

        //si absent vaut true, je ne prend PAS les presences absentes
        if ($absent) {
            $qb->andwhere('p.absent = :absent')
                ->setParameter('absent', 0);
        }

        if ($user) {
            $qb->andwhere('p.user_add = :user')
                ->setParameter('user', $user);
        }

        if ($onlyPaye) {
            $qb->andwhere('p.paiement IS NOT NULL');
        }

        if ($onlyNonPaye) {
            $qb->andwhere('p.paiement IS NULL');
        }

        if ('enfant' == $ordre) {
            $qb->orderBy('e.nom');
        } else {
            $qb->orderBy('j.date_jour', 'DESC');
        }

        $query = $qb->getQuery();

        //echo  $query->getSQL();
        if ($one) {
            return $query->getOneOrNullResult();
        }

        $results = $query->getResult();

        return $results;
    }

    /**
     * Pour une presence je vais voir si fratrie la.
     *
     * @param iterable|Enfant[] $fratries
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function setFratriesByPresence(Presence $presence, iterable $fratries)
    {
        $jour = $presence->getJour();
        $jour_id = $jour->getId();

        $tuteur = $presence->getTuteur();
        $tuteur_id = $tuteur->getId();

        foreach ($fratries as $fratrie) {
            $args = ['jour_id' => $jour_id, 'enfant_id' => $fratrie->getId(), 'tuteur_id' => $tuteur_id];
            $presences_fratries = $this->search($args);

            if (count($presences_fratries) > 0) {
                foreach ($presences_fratries as $presences_fratrie) {
                    $la = $presences_fratrie->getEnfant();
                    $presence->addFratrie($la);
                }
            }
        }
    }

    /**
     * Retourne les presences non payes et
     * qui ne sont pas a payer (gratuite) !
     *
     * @param $args
     *
     * @return Presence[]|\Doctrine\ORM\QueryBuilder
     */
    public function getPresencesNonPayes($args)
    {
        $enfant_id = isset($args['enfant_id']) ? $args['enfant_id'] : null;
        $tuteur_id = isset($args['tuteur_id']) ? $args['tuteur_id'] : null;
        $result = isset($args['result']) ? $args['result'] : null;
        $date = isset($args['date']) ? $args['date'] : null;
        $today = isset($args['today']) ? $args['today'] : null;
        $paiement = isset($args['paiement']) ? $args['paiement'] : null;
        $order = isset($args['order']) ? $args['order'] : null;

        $qb = $this->createQueryBuilder('p');

        $qb->leftJoin('p.jour', 'j', 'WITH');
        $qb->leftJoin('p.enfant', 'e', 'WITH');
        $qb->leftJoin('p.tuteur', 't', 'WITH');
        $qb->leftJoin('p.reduction', 'r', 'WITH');
        $qb->addSelect('j', 'e', 'r', 't');

        if ($enfant_id) {
            $qb->andwhere('p.enfant = :enfant')
                ->setParameter('enfant', $enfant_id);
        }

        if ($tuteur_id) {
            $qb->andwhere('p.tuteur = :tuteur')
                ->setParameter('tuteur', $tuteur_id);
        }

        $qb->andwhere('r.pourcentage IS NULL OR r.pourcentage != :pourcent')
            ->setParameter('pourcent', 100);

        $qb->andWhere('p.absent != 1'); //absent avec certificat

        /*
         * je desire avoir aussi celles qui sont payes
         * (utilise pour modifier les presences sur un paiement
         */
        if ($paiement) {
            $qb->andwhere('p.paiement IS NULL OR p.paiement = :paiement')
                ->setParameter('paiement', $paiement);
        } else {
            $qb->andwhere('p.paiement IS NULL');
        }

        if ($date) {
            $qb->andwhere('j.date_jour LIKE :date')
                ->setParameter('date', '%'.$date.'%');
        }

        if ($today) {
            $today = new \DateTime();
            $qb->andwhere('j.date_jour <= :date')
                ->setParameter('date', $today);
        }

        if ($order) {
            $qb->addOrderBy('e.nom');
        } else {
            $qb->addOrderBy('j.date_jour');
        }

        if ($result) {
            $query = $qb->getQuery();

            $results = $query->getResult();

            return $results;
        }

        return $qb;
    }

    public function getPresencesNonPayesNew(Enfant $enfant, Tuteur $tuteur)
    {
        $qb = $this->createQueryBuilder('presence');

        $qb->leftJoin('presence.jour', 'jour', 'WITH');
        $qb->leftJoin('presence.enfant', 'enfant', 'WITH');
        $qb->leftJoin('presence.tuteur', 'tuteur', 'WITH');
        $qb->leftJoin('presence.reduction', 'reduction', 'WITH');
        $qb->addSelect('jour', 'enfant', 'reduction', 'tuteur');

        if ($enfant) {
            $qb->andwhere('presence.enfant = :enfant')
                ->setParameter('enfant', $enfant);
        }

        if ($tuteur) {
            $qb->andwhere('presence.tuteur = :tuteur')
                ->setParameter('tuteur', $tuteur);
        }

        $qb->andwhere('reduction.pourcentage IS NULL OR reduction.pourcentage != :pourcent')
            ->setParameter('pourcent', 100);

        $qb->andWhere('presence.absent != 1'); //absent avec certificat

        $qb->andwhere('presence.paiement IS NULL');

        $qb->addOrderBy('jour.date_jour');

        return $query = $qb->getQuery()->getResult();
    }

    public function searchForEcole(iterable $args)
    {
        $nom = $args['nom'] ?? null;
        $ecole = $args['ecole'] ?? null;
        $jour = $args['jour'] ?? null;

        $qb = $this->createQueryBuilder('presence');

        $qb->leftJoin('presence.jour', 'jour', 'WITH');
        $qb->leftJoin('presence.enfant', 'enfant', 'WITH');
        $qb->addSelect('jour', 'enfant');

        if ($jour) {
            $qb->andWhere('presence.jour = :jour')
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
