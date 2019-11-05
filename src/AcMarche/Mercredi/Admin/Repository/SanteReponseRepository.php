<?php

namespace AcMarche\Mercredi\Admin\Repository;

use AcMarche\Mercredi\Admin\Entity\Sante\SanteReponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SanteReponse|null   find($id, $lockMode = null, $lockVersion = null)
 * @method SanteReponse|null   findOneBy(array $criteria, array $orderBy = null)
 * @method SanteReponse[]|null findAll()
 * @method SanteReponse[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SanteReponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SanteReponse::class);
    }

    public function insert(SanteReponse $santeQuestion)
    {
        $this->_em->persist($santeQuestion);
        $this->save();
    }

    public function remove(SanteReponse $santeQuestion)
    {
        $this->_em->remove($santeQuestion);
        $this->save();
    }

    public function save()
    {
        $this->_em->flush();
    }

    public function getBySanteFiche(iterable $santeFiches)
    {
        $qb = $this->createQueryBuilder('sante_reponse');

        $qb->andWhere('sante_reponse.sante_fiche IN (:fiches)')
            ->setParameter('fiches', $santeFiches);

        return $qb->getQuery()->getResult();
    }
}
