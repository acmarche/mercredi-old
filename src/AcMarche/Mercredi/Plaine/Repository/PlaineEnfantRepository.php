<?php

namespace AcMarche\Mercredi\Plaine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use AcMarche\Mercredi\Plaine\Entity\PlaineEnfant;

/**
 * @method PlaineEnfant|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaineEnfant|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaineEnfant[]|null findAll()
 * @method PlaineEnfant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaineEnfantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaineEnfant::class);
    }

    public function insert(PlaineEnfant $plaineEnfant)
    {
        $this->_em->persist($plaineEnfant);
        $this->_em->flush();
    }

    public function save()
    {
        $this->_em->flush();
    }

    /**
     * @param $args
     * @return PlaineEnfant[]
     */
    public function search($args)
    {
        $qb = $this->makeCritere($args);

        $query = $qb->getQuery();

        //echo  $query->getSQL();

        $results = $query->getResult();

        return $results;
    }

    /**
     * @param $args
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function makeCritere($args)
    {
        $enfant_id = isset($args['enfant_id']) ? $args['enfant_id'] : 0;
        $plaine_id = isset($args['plaine_id']) ? $args['plaine_id'] : 0;

        $qb = $this->createQueryBuilder('pe');
        $qb->leftJoin('pe.plaine', 'p', 'WITH');
        $qb->leftJoin('pe.enfant', 'e', 'WITH');
        $qb->leftJoin('pe.presences', 'pr', 'WITH');
        $qb->addSelect('p', 'e', 'pr');

        if ($plaine_id) {
            $qb->andwhere('pe.plaine = :plaine')
                ->setParameter('plaine', $plaine_id);
        }

        if ($enfant_id) {
            $qb->andwhere('pe.enfant = :enfant')
                ->setParameter('enfant', $enfant_id);
        }

        $qb->orderBy('e.nom');

        return $qb;
    }
}
