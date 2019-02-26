<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 17/01/18
 * Time: 11:18
 */

namespace AcMarche\Mercredi\Logger\Events;

use Symfony\Component\EventDispatcher\Event;

abstract class AbstractLoggerEvent extends Event
{
    /**
     * @var null
     */
    protected $entity;

    /**
     * AbstractEvent constructor.
     * @param null $entity
     */
    public function __construct($entity = null)
    {
        $this->entity = $entity;
    }

    /**
     * @return bool|null
     */
    public function getEntity()
    {
        if ($this->entity != null) {
            return $this->entity;
        }
        return false;
    }
}
