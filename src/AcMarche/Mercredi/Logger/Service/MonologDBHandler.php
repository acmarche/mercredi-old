<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 16/01/18
 * Time: 16:18.
 */

namespace AcMarche\Mercredi\Logger\Service;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Logger\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;

class MonologDBHandler extends AbstractProcessingHandler
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * MonologDBHandler constructor.
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    /**
     * Called when writing to our database.
     */
    protected function write(array $record): void
    {
        if ('db' !== $record['channel']) {
            //  return;
        }

        //bug serialisation
        if (isset($record['context']['entity'][0])) {
            $entity = $record['context']['entity'][0];
            if ($entity instanceof Enfant) {
                if ($entity->getFile()) {
                    $entity->setFile(null);
                }

                if ($entity->getFiche()) {
                    $entity->setFiche(null);
                }

                if ($entity->getImage()) {
                    $entity->setImage(null);
                }
            }
        }

        $logEntry = new Log();
        $logEntry->setMessage($this->getMessage($record));
        $logEntry->setLevel($record['level']);
        $logEntry->setLevelName($record['level_name']);
        $logEntry->setExtra($record['extra']);
        $logEntry->setContext($record['context']);

        $this->em->persist($logEntry);
        $this->em->flush();
    }

    protected function getMessage($record)
    {
        $message = $record['message'].' ';
        $data = $record['context']['entity'] ? $record['context']['entity'] : 'no entity';

        if (is_array($data)) {
            $entity = $data[0];
            if (is_object($entity)) {
                return $message.strval($entity);
            }
        }

        return $message.$data;
    }
}
