<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 17/01/18
 * Time: 11:19.
 */

namespace AcMarche\Mercredi\Admin\Events;

use AcMarche\Mercredi\Logger\Events\AbstractLoggerEntitySubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EnfantSubscriber extends AbstractLoggerEntitySubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            EnfantEvent::ENFANT_SHOW => 'onEnfantShow',
            EnfantEvent::ENFANT_EDIT => 'onEnfantEdit',
            EnfantEvent::ENFANT_DOWNLOAD => 'onEnfantDownload',
        ];
    }

    public function onEnfantShow(EnfantEvent $event)
    {
        $this->logEntity(
            EnfantEvent::ENFANT_SHOW,
            [
                $event->getEntity(),
            ]
        );
    }

    public function onEnfantEdit(EnfantEvent $event)
    {
        $this->logEntity(
            EnfantEvent::ENFANT_EDIT,
            [
                $event->getEntity(),
            ]
        );
    }

    public function onEnfantDownload(EnfantEvent $event)
    {
        $this->logEntity(
            EnfantEvent::ENFANT_DOWNLOAD,
            [
                $event->getEntity(),
            ]
        );
    }
}
