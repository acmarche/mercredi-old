<?php

namespace AcMarche\Mercredi\Plaine\Repository;

use AcMarche\Mercredi\Plaine\Entity\PlaineMax;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PlaineMax|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaineMax|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaineMax[]|null findAll()
 * @method PlaineMax[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaineMaxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaineMax::class);
    }

    public function insert(PlaineMax $plaine)
    {
        $this->_em->persist($plaine);
        $this->_em->flush();
    }

    public function save()
    {
        $this->_em->flush();
    }

}
