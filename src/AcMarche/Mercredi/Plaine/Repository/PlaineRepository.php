<?php

namespace AcMarche\Mercredi\Plaine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use AcMarche\Mercredi\Plaine\Entity\Plaine;

/**
 * @method Plaine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plaine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plaine[]|null findAll()
 * @method Plaine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plaine::class);
    }

    public function insert(Plaine $plaine)
    {
        $this->_em->persist($plaine);
        $this->_em->flush();
    }

    public function save()
    {
        $this->_em->flush();
    }

    public function remove(Plaine $plaine)
    {
        $this->_em->remove($plaine);
        $this->save();
    }

    /**
     * @param $args
     * @return Plaine[]
     */
    public function search($args)
    {
        $nom = isset($args['intitule']) ? $args['intitule'] : null;
        $archive = isset($args['archive']) ? $args['archive'] : false;

        $qb = $this->createQueryBuilder('p');
        $qb->leftJoin('p.jours', 'j', 'WITH');
        $qb->leftJoin('p.enfants', 'e', 'WITH');
        $qb->addSelect('j', 'e');

        if ($nom) {
            $qb->andwhere('p.intitule LIKE :intitule')
                ->setParameter('intitule', '%'.$nom.'%');
        }

        if ($archive) {
            $qb->andwhere('p.archive = 1');
        } else {
            $qb->andwhere('p.archive = 0');
        }

        $qb->orderBy('p.intitule');

        $query = $qb->getQuery();

        //echo  $query->getSQL();

        $results = $query->getResult();

        return $results;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getForSelect()
    {
        $qb = $this->createQueryBuilder('p');

        $qb->orderBy('p.id', 'DESC');

        return $qb;
    }

    /**
     * @return array
     */
    public function getForSearch()
    {
        $qb = $this->createQueryBuilder('plaine');

        $qb->andWhere('plaine.archive = :archive')
            ->setParameter('archive', 0);

        $qb->orderBy('plaine.intitule');
        $query = $qb->getQuery();

        /**
         * @var Plaine[] $results
         */
        $results = $query->getResult();
        $plaines = array();

        foreach ($results as $plaine) {
            $plaines[$plaine->getIntitule()] = $plaine->getId();
        }

        return $plaines;
    }
}
