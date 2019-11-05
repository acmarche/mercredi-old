<?php

namespace AcMarche\Mercredi\Admin\Repository;

use AcMarche\Mercredi\Admin\Entity\Ecole;
use AcMarche\Mercredi\Security\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Ecole|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ecole|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ecole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EcoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ecole::class);
    }

    /**
     * @return Ecole[]
     */
    public function findAll()
    {
        return $this->findBy([], ['nom' => 'ASC']);
    }

    /**
     * @param array $args
     *
     * @return Ecole[]|Ecole
     */
    public function search($args)
    {
        $nom = isset($args['nom']) ? $args['nom'] : null;
        $id = isset($args['id']) ? $args['id'] : null;
        $one = isset($args['one']) ? $args['one'] : null;

        $qb = $this->createQueryBuilder('ecole');
        $qb->leftJoin('ecole.enfants', 'enfants', 'WITH');
        $qb->leftJoin('ecole.users', 'users', 'WITH');
        $qb->addSelect('enfants', 'users');

        if ($nom) {
            $qb->andwhere(
                'ecole.nom LIKE :nom'
            )
                ->setParameter('nom', '%'.$nom.'%');
        }

        if ($id) {
            $qb->andwhere('ecole.id = :id')
                ->setParameter('id', $id);
        }

        $qb->orderBy('ecole.nom');

        $query = $qb->getQuery();

        if ($one) {
            try {
                return $query->getOneOrNullResult();
            } catch (NonUniqueResultException $e) {
            }
        }

        $results = $query->getResult();

        return $results;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getForList()
    {
        $qb = $this->createQueryBuilder('ecole');
        $qb->orderBy('ecole.nom');

        return $qb;
    }

    public function getForSearch(User $user = null)
    {
        $qb = $this->createQueryBuilder('ecole')
            ->leftJoin('ecole.users', 'users');

        if ($user) {
            $qb->andWhere('users IN (:user)')->setParameter('user', $user->getId());
        }

        $qb->orderBy('ecole.nom');
        $query = $qb->getQuery();

        /**
         * @var Ecole[]
         */
        $results = $query->getResult();
        $ecoles = [];

        foreach ($results as $ecole) {
            $ecoles[$ecole->getNom()] = $ecole->getId();
        }

        return $ecoles;
    }

    public function getForSearchByUser(User $user)
    {
        return $this->getForSearch($user);
    }
}
