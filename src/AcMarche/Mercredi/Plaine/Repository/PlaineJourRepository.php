<?php

namespace AcMarche\Mercredi\Plaine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use AcMarche\Mercredi\Plaine\Entity\PlaineJour;

/**
 * @method PlaineJour|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaineJour|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaineJour[]|null findAll()
 * @method PlaineJour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaineJourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaineJour::class);
    }

    public function getRecents(\DateTime $date)
    {
        $qb = $this->createQueryBuilder('jour');
        $dateString = $date->format('Y-m-d');

        $qb->andWhere("jour.date_jour >= :date")
            ->setParameter("date", $dateString);

        $qb->orderBy('jour.date_jour', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $args
     * @return PlaineJour[] |PlaineJour
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function search($args = array())
    {
        $jour_id = isset($args['jour_id']) ? $args['jour_id'] : false;
        $one = isset($args['one']) ? $args['one'] : false;
        $date = isset($args['date']) ? $args['date'] : false;
        $plaine = isset($args['plaine']) ? $args['plaine'] : false;

        $qb = $this->createQueryBuilder('pj');
        $qb->leftJoin('pj.plaine', 'p', 'WITH');
        $qb->leftJoin('pj.presences', 'pr', 'WITH');
        $qb->addSelect('p', 'pr');

        if ($jour_id) {
            $qb->andwhere('pj.id = :id')
                ->setParameter('id', $jour_id);
        }

        if ($date) {
            list($mois, $annee) = explode('/', $date);
            $date_search = $annee.'-'.$mois.'%';
            $qb->andwhere('pj.date_jour LIKE :date')
                ->setParameter('date', $date_search);
        }

        if ($plaine) {
            $qb->andwhere('pj.plaine = :id')
                ->setParameter('id', $plaine);
        }

        $qb->orderBy('pj.date_jour', 'DESC');

        $query = $qb->getQuery();

        //echo  $query->getSQL();

        if ($one) {
            return $query->getOneOrNullResult();
        }

        $results = $query->getResult();

        return $results;
    }

    /**
     * @param array $args
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getForSelect($args = array())
    {
        $plaine = isset($args['plaine']) ? $args['plaine'] : null;

        $qb = $this->createQueryBuilder('pj');

        if ($plaine) {
            $qb->andwhere('pj.plaine = :id')
                ->setParameter('id', $plaine);
        }

        $qb->orderBy('pj.date_jour', 'ASC');

        return $qb;
    }
}
