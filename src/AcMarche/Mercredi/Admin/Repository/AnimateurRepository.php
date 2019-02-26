<?php

namespace AcMarche\Mercredi\Admin\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use AcMarche\Mercredi\Admin\Entity\Animateur;

/**
 * @method Animateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Animateur|null findOneBy(array $criteria, array $orderBy = null)
 * method Animateur[]|null findAll()
 * @method Animateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnimateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Animateur::class);
    }

    /**
     * @return Animateur[]
     */
    public function findAll()
    {
        return $this->findBy(array(), array('nom' => 'ASC'));
    }

    public function insert(Animateur $animateur)
    {
        $this->_em->persist($animateur);
        $this->save();
    }

    public function remove(Animateur $animateur)
    {
        $this->_em->remove($animateur);
        $this->save();
    }

    public function save()
    {
        $this->_em->flush();
    }

    /**
     * @param $args
     * @return Animateur[]
     */
    public function search($args)
    {
        $nom = isset($args['nom']) ? $args['nom'] : null;

        $qb = $this->createQueryBuilder('animateur');
        $qb->leftJoin('animateur.jours', 'jours', 'WITH');
        $qb->leftJoin('animateur.plaines', 'plaines', 'WITH');
        $qb->addSelect('jours', 'plaines');

        if ($nom) {
            $qb->andwhere('animateur.nom LIKE :nom OR animateur.prenom LIKE :nom')
                ->setParameter('nom', '%'.$nom.'%');
        }

        $qb->orderBy('animateur.nom');

        $query = $qb->getQuery();

        $results = $query->getResult();

        return $results;
    }

    public function getForAssociateParent()
    {
        $qb = $this->createQueryBuilder('animateur');
        $qb->andWhere("animateur.user IS NULL");
        $qb->orderBy('animateur.nom');

        return $qb;
    }
}
