<?php

namespace AcMarche\Mercredi\Security\Repository;

use AcMarche\Mercredi\Security\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function search($args)
    {
        $user = isset($args['user']) ? $args['user'] : null;
        $role = isset($args['role']) ? $args['role'] : 0;
        $user_id = isset($args['user_id']) ? $args['user_id'] : 0;

        $qb = $this->createQueryBuilder('g');
        $qb->leftJoin('g.users', 'u', 'WITH');
        $qb->addSelect('u');

        if ($user) {
            $qb->andwhere('g.users IN :user')
                ->setParameter('user', $user);
        }

        $query = $qb->getQuery();

        //echo  $query->getSQL();

        $results = $query->getResult();

        return $results;
    }

    public function getForSearch()
    {
        $qb = $this->createQueryBuilder('g');

        $qb->orderBy('g.name');
        $query = $qb->getQuery();

        $results = $query->getResult();
        $groupes = [];

        foreach ($results as $group) {
            $groupes[$group->getName()] = $group->getId();
        }

        return $groupes;
    }

    public function getForList()
    {
        $qb = $this->createQueryBuilder('g');

        $qb->orderBy('e.name');

        return $qb;
    }
}
