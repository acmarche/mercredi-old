<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 14/08/18
 * Time: 10:31.
 */

namespace AcMarche\Mercredi\Plaine\Service;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class Mailer
{
    /**
     * @var Environment
     */
    private $twig;
    private $rootPath;
    private $emailFrom;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        \Swift_Mailer $mailer,
        Environment $twigEngine,
        RouterInterface $router,
        $emailFrom,
        $rootPath
    ) {
        $this->mailer = $mailer;
        $this->twig = $twigEngine;
        $this->rootPath = $rootPath;
        $this->emailFrom = $emailFrom;
        $this->router = $router;
    }

    /**
     * @param $from string
     * @param $destinataires string|array
     * @param $sujet string
     * @param $body string
     * @param $attachs \Swift_Attachment[]
     */
    public function send($from = null, $destinataires, $sujet, $body, $attachs = [])
    {
        if (!$from) {
            $from = $this->emailFrom;
        }

        $mail = (new \Swift_Message($sujet))
            ->setFrom($from)
            ->setTo($destinataires);
        $mail->setBody($body);

        foreach ($attachs as $attach) {
            $mail->attach($attach);
        }

        $this->mailer->send($mail);
    }

    /**
     * Confirmation plaine.
     *
     * @param Enfant $enfant
     * @param string $email
     * @param null   $groupeScolaire
     */
    public function sendConfirmationInscription($plaine, $enfant, $email, $groupe)
    {
        $body = $this->twig->render(
            'plaine/mail/confirmation_inscription.txt.twig',
            [
                'enfant' => $enfant,
                'plaine' => $plaine,
            ]
        );

        $attach = [];

        $sujet = "Confirmation de l'inscription de ".$enfant->getPrenom();

        $path = $this->rootPath.'/public/courrier/'.$groupe.'.pdf';

        if (is_readable($path)) {
            $attach[] = \Swift_Attachment::fromPath($path);
        }

        $this->send($this->emailFrom, $email, $sujet, $body, $attach);
    }

    public function sendPasEmailPaine(Plaine $plaine, Enfant $enfant)
    {
        $body = 'Validation paiment, attention pas de email';
        $attach = [];

        $destinataires = [$this->emailFrom];
        $sujet = ' ! Pas de email pour '.$enfant->getPrenom().' à '.$plaine->getIntitule();

        try {
            $this->send($this->emailFrom, $destinataires, $sujet, $body, $attach);
        } catch (\Swift_SwiftException $e) {
        }
    }
}
