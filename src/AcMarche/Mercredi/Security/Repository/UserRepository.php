<?php

namespace AcMarche\Mercredi\Security\Repository;

use AcMarche\Mercredi\Security\Entity\Group;
use AcMarche\Mercredi\Security\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function insert(User $user)
    {
        $this->persist($user);
        $this->save();
    }

    public function persist(User $user)
    {
        $this->_em->persist($user);
    }

    public function save()
    {
        $this->_em->flush();
    }

    public function remove(User $user)
    {
        $this->_em->remove($user);
        $this->save();
    }

    /**
     * @param $username
     *
     * @return User
     *
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function search($args)
    {
        $user = isset($args['user']) ? $args['user'] : null;
        $nom = isset($args['nom']) ? $args['nom'] : null;
        $username = isset($args['username']) ? $args['username'] : null;
        $one = isset($args['one']) ? $args['one'] : null;
        $role = isset($args['role']) ? $args['role'] : 0;
        $groupe = isset($args['groupe']) ? $args['groupe'] : 0;

        /**
         * SELECT * FROM `users` LEFT JOIN fos_user_group ON users.`id` = fos_user_group.`user_id`
         * WHERE fos_user_group.`group_id` = 1.
         */
        $qb = $this->createQueryBuilder('user');
        $qb->leftJoin('user.groups', 'groups', 'WITH');
        $qb->addSelect('groups');

        if ($username) {
            $qb->andwhere('user.username = :username')
                ->setParameter('username', $username);
        }

        if ($nom) {
            $qb->andwhere('user.username LIKE :nom OR user.nom LIKE :nom OR user.prenom LIKE :nom')
                ->setParameter('nom', '%'.$nom.'%');
        }

        if ($groupe) {
            $qb->andWhere('groups = :group')
                ->setParameter('group', $groupe);
        }

        $qb->orderBy('user.nom, user.prenom');

        $query = $qb->getQuery();

        //echo  $query->getSQL();
        if ($one) {
            return $query->getOneOrNullResult();
        }

        $results = $query->getResult();

        return $results;
    }

    public function getList($role = null)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->leftJoin('u.groups', 'g', 'WITH');
        $qb->leftJoin('u.profile', 'p', 'WITH');
        $qb->addSelect('g', 'p');

        if ($role) {
        }

        $qb->orderBy('u.nom, u.prenom');
        $query = $qb->getQuery();
        /**
         * @var User[]
         */
        $results = $query->getResult();

        $users = [];
        foreach ($results as $result) {
            $users[$result->getUsername()] = mb_strtoupper($result->getNom(), 'UTF-8').' '.$result->getPrenom();
        }

        return $users;
    }

    public function getForList(Group $group = null)
    {
        $qb = $this->createQueryBuilder('user');
        $qb->leftJoin('user.groups', 'groups', 'WITH');
        $qb->addSelect('groups');

        if ($group) {
            $qb->andwhere('groups = :group')
                ->setParameter('group', $group);
        }

        $qb->orderBy('user.nom', 'ASC');

        return $qb;
    }
}
