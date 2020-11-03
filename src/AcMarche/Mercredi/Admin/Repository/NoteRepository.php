<?php

namespace AcMarche\Mercredi\Admin\Repository;

use AcMarche\Mercredi\Admin\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Note|null   find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null   findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]|null findAll()
 * @method Note[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function insert(Note $note)
    {
        $this->_em->persist($note);
        $this->_em->flush();
    }

    public function save()
    {
        $this->_em->flush();
    }

    public function delete(Note $note)
    {
        $this->_em->remove($note);
        $this->save();
    }

    /**
     * @return Note[]|Note
     */
    public function search(array $args)
    {
        $archive = isset($args['archive']) ? $args['archive'] : false;
        $enfant = isset($args['enfant']) ? $args['enfant'] : false;
        $one = isset($args['one']) ? $args['one'] : false;

        $qb = $this->createQueryBuilder('n');
        $qb->leftJoin('n.enfant', 'enfant', 'WITH');
        $qb->addSelect('enfant');

        if ($archive) {
            $qb->andwhere('n.cloture = 1');
        } else {
            $qb->andwhere('n.cloture != 1');
        }

        if ($enfant) {
            $qb->andwhere('enfant = :id')
                ->setParameter('id', $enfant);
        }

        $qb->orderBy('n.created', 'DESC');

        $query = $qb->getQuery();

        //echo  $query->getSQL();
        if ($one) {
            return $query->getOneOrNullResult();
        }

        $results = $query->getResult();

        return $results;
    }
}
