<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 23/01/18
 * Time: 11:42
 */

namespace AcMarche\Mercredi\Security\Service;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Security\Entity\User;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;

class Mailer
{

    private $emailFrom;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var EngineInterface
     */
    private $twigEngine;

    public function __construct(
        \Swift_Mailer $mailer,
        EngineInterface $twigEngine,
        RouterInterface $router,
        $emailFrom
    ) {
        $this->mailer = $mailer;
        $this->emailFrom = $emailFrom;
        $this->router = $router;
        $this->twigEngine = $twigEngine;
    }

    /**
     * @param User $user
     * @param Tuteur $tuteur
     * @param string|null $password
     */
    public function sendNewAccountToParent(User $user, Tuteur $tuteur, string $password = null)
    {
        $body = $this->twigEngine->render(
            'security/mail/new_account_parent.txt.twig',
            array(
                'tuteur' => $tuteur,
                'user' => $user,
                'password' => $password,
            )
        );

        $mail = (new \Swift_Message("Votre compte pour le site du mercredi"))
            ->setFrom($this->emailFrom)
            ->setTo($user->getEmail())
            ->setBody($body, 'text/plain')
            ->setBcc($this->emailFrom);

        $this->mailer->send($mail);
    }

    /**
     * @param User $user
     * @param Animateur $animateur
     * @param string|null $password
     */
    public function sendNewAccountToAnimateur(User $user, Animateur $animateur, string $password = null)
    {
        $body = $this->twigEngine->render(
            'security/mail/new_account_animateur.txt.twig',
            array(
                'tuteur' => $animateur,
                'user' => $user,
                'password' => $password,
            )
        );

        $mail = (new \Swift_Message("Votre compte pour le site du mercredi"))
            ->setFrom($this->emailFrom)
            ->setTo($user->getEmail())
            ->setBody($body, 'text/plain')
            ->setBcc($this->emailFrom);

        $this->mailer->send($mail);
    }

     /**
     * @param User $user
     * @param string|null $password
     */
    public function sendNewAccountToEcole(User $user, string $password = null)
    {
        $body = $this->twigEngine->render(
            'security/mail/new_account_ecole.txt.twig',
            array(
                'user' => $user,
                'password' => $password,
            )
        );

        $mail = (new \Swift_Message("Votre compte pour le site du mercredi"))
            ->setFrom($this->emailFrom)
            ->setTo($user->getEmail())
            ->setBody($body, 'text/plain')
            ->setBcc($this->emailFrom);

        $this->mailer->send($mail);
    }
}
