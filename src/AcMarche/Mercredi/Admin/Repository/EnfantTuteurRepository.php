<?php

namespace AcMarche\Mercredi\Admin\Repository;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EnfantTuteur|null   find($id, $lockMode = null, $lockVersion = null)
 * @method EnfantTuteur|null   findOneBy(array $criteria, array $orderBy = null)
 * @method EnfantTuteur[]|null findAll()
 * @method EnfantTuteur[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnfantTuteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnfantTuteur::class);
    }

    public function insert(EnfantTuteur $enfantTuteur)
    {
        $this->_em->persist($enfantTuteur);
        $this->save();
    }

    public function save()
    {
        $this->_em->flush();
    }

    public function remove(EnfantTuteur $enfantTuteur)
    {
        $this->_em->remove($enfantTuteur);
        $this->save();
    }

    /**
     * @return Tuteur[]
     */
    public function getTuteursByEnfant(Enfant $enfant)
    {
        $results = $this->findBy(['enfant' => $enfant]);
        $tuteurs = [];

        foreach ($results as $result) {
            $tuteurs[] = $result->getTuteur();
        }

        return $tuteurs;
    }

    /**
     * @param Enfant[] $enfants
     *
     * @return Tuteur[]
     */
    public function getTuteursByEnfants($enfants)
    {
        $results = $this->findBy(['enfant' => $enfants]);
        $tuteurs = [];

        foreach ($results as $result) {
            $tuteurs[] = $result->getTuteur();
        }

        return array_unique($tuteurs);
    }

    /**
     * @param $args
     *
     * @return EnfantTuteur[]|EnfantTuteur
     */
    public function search($args)
    {
        $enfant_id = isset($args['enfant_id']) ? $args['enfant_id'] : 0;
        $tuteur_id = isset($args['tuteur_id']) ? $args['tuteur_id'] : 0;
        $ordre = isset($args['ordre']) ? $args['ordre'] : 0;
        $one = isset($args['one']) ? $args['one'] : 0;

        $qb = $this->createQueryBuilder('enfantTuteur');
        $qb->leftJoin('enfantTuteur.enfant', 'enfant', 'WITH');
        $qb->leftJoin('enfantTuteur.tuteur', 'tuteur', 'WITH');
        $qb->addSelect('enfant', 'tuteur');

        if ($enfant_id) {
            $qb->andwhere('enfantTuteur.enfant = :enfant')
                ->setParameter('enfant', $enfant_id);
        }

        if ($tuteur_id) {
            if (is_array($tuteur_id)) {
                $tuteur_id = array_unique($tuteur_id);
                $tuteurs_string = implode(',', $tuteur_id);
                $qb->andWhere('enfantTuteur.tuteur IN ('.$tuteurs_string.')');
            //  $qb->groupBy('et.enfant'); //sinon jai doublon
            } else {
                $qb->andwhere('enfantTuteur.tuteur = :tuteur')
                    ->setParameter('tuteur', $tuteur_id);
            }
        }

        if ($ordre) {
            $qb->andwhere('enfantTuteur.ordre LIKE :ordre ')
                ->setParameter('ordre', $ordre);
        }

        $qb->orderBy('enfantTuteur.id');

        $query = $qb->getQuery();

        if ($one) {
            return $query->getOneOrNullResult();
        }

        $results = $query->getResult();

        return $results;
    }

    public function getEntantsActif(Tuteur $tuteur)
    {
        $qb = $this->createQueryBuilder('enfantTuteur');
        $qb->leftJoin('enfantTuteur.enfant', 'enfant', 'WITH');
        $qb->addSelect('enfant');

        $qb->andwhere('enfantTuteur.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur);

        $qb->andwhere('enfant.archive != 1');

        $query = $qb->getQuery();

        return $query->getResult();
    }
}
