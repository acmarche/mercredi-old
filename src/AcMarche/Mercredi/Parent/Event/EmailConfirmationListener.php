<?php

namespace AcMarche\Mercredi\Parent\Event;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailConfirmationListener implements EventSubscriberInterface
{
    private $router;
    private $container;
    private $mailer;

    public function __construct(
        UrlGeneratorInterface $router,
        ContainerInterface $containerInterface,
        \Swift_Mailer $mailer
    ) {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->container = $containerInterface;
    }

    //todo replace event
    public static function getSubscribedEvents()
    {
        return [];
        /*
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => array(
                array('onRegistrationSuccess', -10),
            ),
        );*/
    }

    /**
     * @param FormEvent $formEventhttps ://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Controller/RegistrationController.php#L68
     */
    public function onRegistrationSuccess(FormEvent $formEvent)
    {
        //AcMarche\Admin\Security\Entity\User
        $user = $formEvent->getForm()->getData();
        $nom = $user->getNom().' '.$user->getPrenom();

        $from = $this->container->getParameter('enfance_email_from');
        $message = new \Swift_Message();
        $message->setSubject('Nouvel utilisateur : '.$user->getUsername().' sur admin');
        $message->setFrom($from);
        $message->addTo($from);
        $message->setBcc('jf@marche.be');
        $message->setBody(
            'Allez associer le compte de '.$nom.' à un tuteur sur https://admin.marche.be/admin/utilisateurs/'
        );

        $this->mailer->send($message);
    }
}
