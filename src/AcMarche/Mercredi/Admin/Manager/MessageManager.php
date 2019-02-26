<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 26/09/18
 * Time: 16:25
 */

namespace AcMarche\Mercredi\Admin\Manager;

use AcMarche\Mercredi\Admin\Entity\Message;
use AcMarche\Mercredi\Admin\Repository\MessageRepository;
use AcMarche\Mercredi\Admin\Service\MailerService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MessageManager
{
    /**
     * @var MailerService
     */
    private $mailerService;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var MessageRepository
     */
    private $messageRepository;

    public function __construct(MessageRepository $messageRepository, MailerService $mailerService, ParameterBagInterface $parameterBag)
    {
        $this->mailerService = $mailerService;
        $this->parameterBag = $parameterBag;
        $this->messageRepository = $messageRepository;
    }

    /**
     * @return Message
     */
    public function newInstance()
    {
        $message = new Message();
        $message->setFrom($this->parameterBag->get('enfance_email_from'));

        return $message;
    }

    public function handleMessage(Message $message, $destinataires)
    {
        $sujet = $message->getSujet();
        $body = $message->getTexte();
        $file = $message->getFile();
        $message->setDestinataires($destinataires);

        $this->mailerService->sendToParent($sujet, $body, $destinataires, $file);

        $this->messageRepository->insert($message);
    }

    public function sendTest($sujet, $body, $email)
    {
        $this->mailerService->sendTestToParent($sujet, $body, $email);
    }

    /**
     * @return Message[]|null
     */
    public function getAll() {
        return $this->messageRepository->findAll();
    }
}