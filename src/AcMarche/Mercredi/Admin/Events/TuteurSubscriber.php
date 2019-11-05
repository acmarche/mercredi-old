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

class TuteurSubscriber extends AbstractLoggerEntitySubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            TuteurEvent::TUTEUR_SHOW => 'onTuteurShow',
            TuteurEvent::TUTEUR_EDIT => 'onTuteurEdit',
        ];
    }

    public function onTuteurShow(TuteurEvent $event)
    {
        $this->logEntity(
            TuteurEvent::TUTEUR_SHOW,
            [
                 $event->getEntity(),
            ]
        );
    }

    public function onTuteurEdit(TuteurEvent $event)
    {
        $this->logEntity(
            TuteurEvent::TUTEUR_EDIT,
            [
                $event->getEntity(),
            ]
        );
    }
}
