<?php

namespace AcMarche\Mercredi\Admin\Repository;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Jour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Jour|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jour|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jour::class);
    }

    /**
     * @return Jour[]
     */
    public function findAll()
    {
        return $this->findBy(array(), array('date_jour' => 'ASC'));
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
     * Pour formulaire avec liste deroulante
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getForList(Enfant $enfant = null)
    {
        $qb = $this->createQueryBuilder('j');

        /**
         * je retire les dates pour lesquelles l'enfant est
         * deja inscrits
         */
        if ($enfant) {
            $presences = $enfant->getPresences();
            $jours = array();
            foreach ($presences as $presence) {
                $jour_id = $presence->getJour()->getId();
                $jours[] = $jour_id;
            }
            $jours = array_unique($jours);
            $jours_string = implode("','", $jours);

            $qb->andWhere('j.id NOT IN (\''.$jours_string.'\')');
        }

        $qb->andWhere('j.archive = :archive')
            ->setParameter('archive', 0);

        $qb->andwhere('j.archive = 0');
        $qb->orderBy('j.date_jour', 'DESC');

        return $qb;
    }

    /**
     * Pour formulaire avec liste deroulante
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getForAnimateur(Animateur $animateur = null)
    {
        $qb = $this->createQueryBuilder('j');

        /**
         * je retire les dates pour lesquelles l'animateur est
         * deja inscrits
         */
        if ($animateur) {
        }

        $qb->andWhere('j.archive = :today')
            ->setParameter('today', 0);

        $qb->orderBy('j.date_jour', 'DESC');

        return $qb;
    }

    /**
     * @param array $args
     * @return Jour|Jour[]
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function search($args = array())
    {
        $jour_id = isset($args['jour_id']) ? $args['jour_id'] : false;
        $one = isset($args['one']) ? $args['one'] : false;
        $date = isset($args['date']) ? $args['date'] : false;
        $order = isset($args['order']) ? $args['order'] : false;
        $archive = isset($args['archive']) ? $args['archive'] : false;
        $dateComplete = isset($args['datecomplete']) ? $args['datecomplete'] : false;

        $qb = $this->createQueryBuilder('j');
        $qb->leftJoin('j.presences', 'p', 'WITH');
        $qb->leftJoin('j.animateurs', 'a', 'WITH');

        $qb->addSelect('p', 'a');

        if ($jour_id) {
            $qb->andWhere('j.id = :id')
                ->setParameter('id', $jour_id);
        }

        if ($date) {
            list($mois, $annee) = explode('/', $date);
            $date_search = $annee.'-'.$mois.'%';
            $qb->andWhere('j.date_jour LIKE :date')
                ->setParameter('date', $date_search);
        }

        if ($dateComplete) {
            $qb->andWhere('j.date_jour LIKE :date')
                ->setParameter('date', $dateComplete);
        }

        if (is_array($order)) {
            $qb->orderBy($order[0], $order[1]);
        } else {
            $qb->orderBy('p.enfant');
        }

        if ($archive) {
            $qb->andWhere('j.archive = 1');
        } else {
            $qb->andWhere('j.archive = 0');
        }

        $query = $qb->getQuery();

        if ($one) {
            return $query->getOneOrNullResult();
        }

        return $query->getResult();
    }

    /**
     * @param Enfant|null $enfant
     * @return Jour[]
     * @throws \Exception
     */
    public function getForParent(Enfant $enfant = null)
    {
        $qb = $this->createQueryBuilder('jour');

        /**
         * je retire les dates pour lesquelles l'enfant est
         * deja inscrits
         */
        if ($enfant) {
            $presences = $enfant->getPresences();
            if (count($presences) > 0) {
                $jours = array();
                foreach ($presences as $presence) {
                    $jours[] = $presence->getJour();
                }
                $qb->andWhere("jour NOT IN (:jours)")
                    ->setParameter('jours', $jours);
            }
        }

        /**
         * je ne propose pas les dates passees
         */
        $date_time = new \DateTime();
        $today = $date_time->format("Y-m-d");

        $qb->andWhere('jour.date_jour >= :today')
            ->setParameter('today', $today);

        $qb->andWhere('jour.archive = 0');
        $qb->orderBy('jour.date_jour', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function getForEcoleToSearch()
    {
        $qb = $this->createQueryBuilder('jour');

        $date_time = new \DateTime();

        $qb->andWhere('jour.date_jour >= :today')
            ->setParameter('today', $date_time);

        $query = $qb->getQuery();
        /**
         * @var Jour[] $results
         */
        $results = $query->getResult();
        $jours = [];

        foreach ($results as $jour) {
            $dateJour = $jour->getDateJour();
            $numericDay = $dateJour->format('w');

            if ($numericDay == 3) {
                $jours[$dateJour->format('d-m-Y')] = $jour->getId();
            }
        }

        return $jours;
    }

    /**
     * @return array
     */
    public function getForSearch()
    {
        $qb = $this->createQueryBuilder('jour');
        $qb->orderBy('jour.date_jour', 'DESC');
        $query = $qb->getQuery();
        /**
         * @var Jour[] $results
         */
        $results = $query->getResult();
        $jours = [];

        foreach ($results as $jour) {
            $jours[$jour->getDateJour()->format('d-m-Y')] = $jour->getId();
        }

        return $jours;
    }
}
