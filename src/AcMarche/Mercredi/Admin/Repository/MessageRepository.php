<?php

namespace AcMarche\Mercredi\Admin\Repository;

use AcMarche\Mercredi\Admin\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Message|null   find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null   findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]|null findAll()
 * @method Message[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function insert(Message $message)
    {
        $this->_em->persist($message);
        $this->save();
    }

    public function remove(Message $message)
    {
        $this->_em->remove($message);
        $this->save();
    }

    public function save()
    {
        $this->_em->flush();
    }
}
