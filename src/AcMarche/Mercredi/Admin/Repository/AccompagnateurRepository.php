<?php

namespace AcMarche\Mercredi\Admin\Repository;

use AcMarche\Mercredi\Admin\Entity\Accompagnateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Accompagnateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Accompagnateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Accompagnateur[]    findAll()
 * @method Accompagnateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccompagnateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Accompagnateur::class);
    }

    /**
     * @return Accompagnateur[]
     */
    public function findByEcoles(array $ecoles)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.ecole IN (:val)')
            ->setParameter('val', $ecoles)
            ->orderBy('a.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
