<?php

namespace AcMarche\Mercredi\Admin\Repository;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Tuteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tuteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tuteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TuteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tuteur::class);
    }

    public function insert(Tuteur $tuteur)
    {
        $this->_em->persist($tuteur);
        $this->_em->flush();
    }

    public function save()
    {
        $this->_em->flush();
    }

    /**
     * @return Tuteur[]
     */
    public function findAll()
    {
        return $this->findBy([], ['nom' => 'ASC']);
    }

    /**
     * @param $nom
     *
     * @return Tuteur[]
     */
    public function findForAutoComplete($nom)
    {
        $qb = $this->createQueryBuilder('tuteur');

        $qb->andwhere(
            'tuteur.nom LIKE :nom OR tuteur.prenom LIKE :nom OR tuteur.nom_conjoint LIKE :nom OR tuteur.prenom_conjoint LIKE :nom'
        )
            ->setParameter('nom', '%'.$nom.'%');

        $qb->orderBy('tuteur.nom');

        $query = $qb->getQuery();

        $results = $query->getResult();

        return $results;
    }

    /**
     * Pour la page index pour plus de rapidite.
     *
     * @return Tuteur[]
     */
    public function getAll()
    {
        $qb = $this->createQueryBuilder('tuteur');
        $qb->leftJoin('tuteur.enfants', 'enfant', 'WITH');
        $qb->leftJoin('tuteur.paiements', 'paiement', 'WITH');
        $qb->addSelect('enfant', 'paiement');

        $qb->orderBy('tuteur.nom');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * Recherche d'un tuteur suivant son nom.
     *
     * @param array $args
     *
     * @return Tuteur[]|Tuteur
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function search($args)
    {
        $nom = isset($args['nom']) ? $args['nom'] : null;
        $slugname = isset($args['slugname']) ? $args['slugname'] : null;
        $id = isset($args['id']) ? $args['id'] : null;
        $one = isset($args['one']) ? $args['one'] : null;

        $qb = $this->createQueryBuilder('tuteur');
        $qb->leftJoin('tuteur.enfants', 'enfant', 'WITH');
        $qb->leftJoin('tuteur.paiements', 'paiement', 'WITH');
        $qb->leftJoin('tuteur.user', 'user', 'WITH');
        $qb->leftJoin('tuteur.presences', 'presences', 'WITH');
        $qb->addSelect('enfant', 'paiement', 'user', 'presences');

        if ($nom) {
            $qb->andwhere(
                'tuteur.nom LIKE :nom OR tuteur.prenom LIKE :nom OR tuteur.nom_conjoint LIKE :nom 
                OR tuteur.prenom_conjoint LIKE :nom OR tuteur.email LIKE :nom OR tuteur.email_conjoint LIKE :nom'
            )
                ->setParameter('nom', '%'.$nom.'%');
        }

        if ($slugname) {
            $qb->andwhere('tuteur.slugname = :slugname')
                ->setParameter('slugname', $slugname);
        }

        if ($id) {
            $qb->andwhere('tuteur.id = :id')
                ->setParameter('id', $id);
        }

        $qb->orderBy('tuteur.nom');

        $query = $qb->getQuery();

        if ($one) {
            return $query->getOneOrNullResult();
        }

        $results = $query->getResult();

        return $results;
    }

    /**
     * Recherche d'un tuteur suivant son nom.
     *
     * @param array $args
     *
     * @return Tuteur[]|Tuteur
     */
    public function quickSearch($args)
    {
        $nom = isset($args['nom']) ? $args['nom'] : null;

        $qb = $this->createQueryBuilder('tuteur');
        $qb->leftJoin('tuteur.enfants', 'enfant', 'WITH');
        $qb->leftJoin('tuteur.paiements', 'paiement', 'WITH');
        $qb->addSelect('enfant', 'paiement');

        if ($nom) {
            $qb->andwhere(
                'tuteur.nom LIKE :nom OR tuteur.prenom LIKE :nom OR tuteur.nom_conjoint LIKE :nom 
                OR tuteur.prenom_conjoint LIKE :nom OR tuteur.email LIKE :nom OR tuteur.email_conjoint LIKE :nom'
            )
                ->setParameter('nom', '%'.$nom.'%');
        }

        $qb->andWhere('tuteur.archive != :archive')
            ->setParameter(':archive', true);

        $qb->orderBy('tuteur.nom');
        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @param $tuteur_id
     *
     * @return Enfant[]
     */
    public function getEnfants($tuteur_id)
    {
        $em = $this->getEntityManager();

        $enfant_tuteurs = $em->getRepository(EnfantTuteur::class)->search(['tuteur_id' => $tuteur_id]);
        $enfants = [];

        foreach ($enfant_tuteurs as $enfant_tuteur) {
            $enfant = $enfant_tuteur->getEnfant();
            $enfants[] = $enfant;
        }

        return $enfants;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getTuteursForList(Enfant $enfant)
    {
        $qb = $this->createQueryBuilder('t');
        $em = $this->getEntityManager();
        $args = ['enfant_id' => $enfant->getId()];

        $enfantTuteurs = $em->getRepository(EnfantTuteur::class)->search($args);
        if (0 == count($enfantTuteurs)) {
            $qb->andwhere('t.id IN ('. 0 .')');

            return $qb;
        }

        $tuteurIds = array_map(
            function ($e) {
                return is_object($e) ? $e->getTuteur()->getId() : 0;
            },
            $enfantTuteurs
        );

        $ids = join(',', $tuteurIds);

        $qb->andwhere('t.id IN ('.$ids.')');

        return $qb;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getForList()
    {
        $qb = $this->createQueryBuilder('t');
        $qb->orderBy('t.nom');

        return $qb;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getForAssociateParent()
    {
        $qb = $this->createQueryBuilder('t');
        $qb->andWhere('t.user IS NULL');
        $qb->orderBy('t.nom');

        return $qb;
    }

    /**
     * @return Tuteur|null
     */
    public function findOneByEmail(string $email)
    {
        $qb = $this->createQueryBuilder('tuteur');

        $qb->andWhere('tuteur.email = :email or tuteur.email_conjoint = :email')
            ->setParameter('email', $email);

        $query = $qb->getQuery();

        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
