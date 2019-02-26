<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 23/01/18
 * Time: 9:08
 */

namespace AcMarche\Mercredi\Parent\Event;

use AcMarche\Mercredi\Admin\Exception\RedirectException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Cette classe devrait m'éviter de faire dans un controller :
 * try {
            $tuteur = $tuteurUtils->userHasTuteur($user);
        } catch (RedirectException $e) {
            return $e->getRedirectResponse();
        }
 * Mais ca marche pas...
 * Inspiration: https://www.trisoft.ro/blog/56-symfony-redirecting-from-outside-the-controller
 * Class RedirectExceptionListener
 * @package Enfance\ParentBundle\Event
 */
class RedirectExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($event->getException() instanceof RedirectException) {
            $event->setResponse($event->getException()->getRedirectResponse());
        }
    }
}
