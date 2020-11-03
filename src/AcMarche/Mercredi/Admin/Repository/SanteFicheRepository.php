<?php

namespace AcMarche\Mercredi\Admin\Repository;

use AcMarche\Mercredi\Admin\Entity\Sante\SanteFiche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SanteFiche|null   find($id, $lockMode = null, $lockVersion = null)
 * @method SanteFiche|null   findOneBy(array $criteria, array $orderBy = null)
 * @method SanteFiche[]|null findAll()
 * @method SanteFiche[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SanteFicheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SanteFiche::class);
    }

    public function insert(SanteFiche $enfant)
    {
        $this->_em->persist($enfant);
        $this->save();
    }

    public function remove(SanteFiche $enfant)
    {
        $this->_em->remove($enfant);
        $this->save();
    }

    public function save()
    {
        $this->_em->flush();
    }

    public function getByEnfants(iterable $enfants)
    {
        $qb = $this->createQueryBuilder('sante_fiche');

        $qb->andWhere('sante_fiche.enfant IN (:enfants)')
            ->setParameter('enfants', $enfants);

        return $qb->getQuery()->getResult();
    }
}
