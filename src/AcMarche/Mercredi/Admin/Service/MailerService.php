<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 23/01/18
 * Time: 11:42.
 */

namespace AcMarche\Mercredi\Admin\Service;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Note;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Security\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Twig\Environment;

class MailerService
{
    protected $mail;
    protected $twig;
    protected $rootPath;
    protected $emailFrom;

    public function __construct(\Swift_Mailer $mailer, Environment $twigEngine, $emailFrom, $rootPath)
    {
        $this->mailer = $mailer;
        $this->twig = $twigEngine;
        $this->rootPath = $rootPath;
        $this->emailFrom = $emailFrom;
    }

    public function sendToParent($sujet, $body, array $destinataires, UploadedFile $uploadedFile = null)
    {
        $message = (new \Swift_Message($sujet))
            ->setFrom($this->emailFrom)
            ->setBody($body);

        if ($uploadedFile) {
            $attach = \Swift_Attachment::fromPath($uploadedFile)
                ->setFilename(
                    $uploadedFile->getClientOriginalName()
                )
                ->setContentType(
                    $uploadedFile->getClientMimeType()
                );
            $message->attach($attach);
        }

        foreach ($destinataires as $destinataire) {
            $message->setTo($destinataire);
            $this->mailer->send($message);
        }
    }

    public function sendTestToParent($sujet, $body, $to)
    {
        $message = (new \Swift_Message($sujet))
            ->setFrom($this->emailFrom)
            ->setBody($body);

        $message->setTo($to);
        $this->mailer->send($message);
    }

    public function sendNote(Note $note)
    {
        $message = (new \Swift_Message('Note clôturée pour '.$note->getEnfant()))
            ->setFrom($this->emailFrom)
            ->setBody($note->getContenu())
            ->setTo($this->emailFrom);

        $this->mailer->send($message);
    }

    public function sendPresenceDeletedByParent(Presence $presence, User $user)
    {
        $body = $this->twig->render(
            'parent/jour/mail.html.twig',
            [
                'presence' => $presence,
                'user' => $user,
            ]
        );

        $sujet = $user->getNom().' '.$user->getPrenom().' a supprimé une présence';

        $message = (new \Swift_Message($sujet))
            ->setFrom($user->getEmail())
            ->setBody($body, 'text/html')
            ->setTo($this->emailFrom);

        $this->mailer->send($message);
    }

    public function sendContactTuteurChange(Tuteur $tuteur, Tuteur $oldTuteur, $from)
    {
        $sujet = $tuteur->getNom().' '.$tuteur->getPrenom().' a modifié ses coordonnées';

        $body = $this->twig->render(
            'parent/tuteur/mail.html.twig',
            [
                'new' => $tuteur,
                'old' => $oldTuteur,
            ]
        );

        $message = (new \Swift_Message($sujet))
            ->setFrom($from)
            ->setBody($body, 'text/html')
            ->setTo($this->emailFrom);

        $this->mailer->send($message);
    }

    public function sendTuteurAddEnfantToPlaine(Enfant $enfant, Tuteur $tuteur, Plaine $plaine, User $user)
    {
        $sujet = $tuteur->getNom().' '.$tuteur->getPrenom().' a inscrit à la plaine '.$plaine->getIntitule();
        $body = 'Pour '.$enfant->getPrenom().' '.$enfant->getNom();
        $from = $user->getEmail();

        $message = (new \Swift_Message($sujet))
            ->setFrom($from)
            ->setBody($body)
            ->setTo($this->emailFrom);

        $this->mailer->send($message);
    }

    public function sendPlaineFull(Enfant $enfant, Tuteur $tuteur, Plaine $plaine, User $user)
    {
        $sujet = $tuteur->getNom().' '.$tuteur->getPrenom().' ! plus de place ! '.$plaine->getIntitule();
        $body = 'Pour '.$enfant->getPrenom().' '.$enfant->getNom();
        $from = $user->getEmail();

        $message = (new \Swift_Message($sujet))
            ->setFrom($from)
            ->setBody($body)
            ->setTo($this->emailFrom);

        $this->mailer->send($message);
    }

    public function sendContactForm($from, $nom, $body)
    {
        $message = (new \Swift_Message());

        $message->setFrom($from);
        $message->setSubject("$nom vous contact via le site du mercredi");
        $message->setBody($body);
        $message->setTo($this->emailFrom);

        $this->mailer->send($message);
    }

    public function sendFicheSanteUpdate(SanteFiche $santeFiche, User $user)
    {
        $enfant = $santeFiche->getEnfant();
        $sujet = 'Mise à jour de la fiche santé de '.$enfant->getNom().' '.$enfant->getPrenom();

        $body = $this->twig->render(
            'parent/sante/mail.html.twig',
            [
                'enfant' => $enfant,
                'santeFiche' => $santeFiche,
                'user' => $user,
            ]
        );

        $message = (new \Swift_Message($sujet))
            ->setFrom($user->getEmail())
            ->setBody($body)
            ->setTo($this->emailFrom);

        $this->mailer->send($message);
    }
}
