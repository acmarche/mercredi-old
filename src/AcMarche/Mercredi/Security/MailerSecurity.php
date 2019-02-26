<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 10/07/18
 * Time: 14:16
 */

namespace AcMarche\Mercredi\Security;

use AcMarche\Mercredi\Security\Entity\User;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;

class MailerSecurity
{
    protected $mail;
    protected $twig;
    protected $rootPath;
    protected $emailFrom;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(\Swift_Mailer $mailer, EngineInterface $twigEngine, ParameterBagInterface $parameterBag, RouterInterface $router)
    {
        $this->mailer = $mailer;
        $this->twig = $twigEngine;
        $this->emailFrom = $parameterBag->get('enfance_email_from');
        $this->parameterBag = $parameterBag;
        $this->router = $router;
    }

    /**
     * @param $from string
     * @param $destinataires string|array
     * @param $sujet string
     * @param $body string
     * @param $attachs \Swift_Attachment[]
     */
    public function send($from, $destinataires, $sujet, $body, $attachs = [])
    {
        $mail = (new \Swift_Message($sujet))
            ->setFrom($from)
            ->setTo($destinataires);
        $mail->setBody($body);

        foreach ($attachs as $attach) {
            $mail->attach($attach);
        }

        $this->mailer->send($mail);
    }


    public function sendRequestNewPassword(User $user)
    {
        $url = $this->router->generate('mercredi_password_reset', ['token'=>$user->getConfirmationToken()]);
        $body = $this->twig->render(
            'security/mail/request_password.txt.twig',
            [
                'user' => $user,
                'url' => $url,
            ]
        );

        $sujet = "Mercredi: Demande d'un nouveau mot de passe";

        $this->send($this->emailFrom, $user->getEmail(), $sujet, $body);
    }
}
