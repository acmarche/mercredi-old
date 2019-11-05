<?php

namespace AcMarche\Mercredi\Admin\Command;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Repository\EnfantRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;

class CheckAttachmentCommand extends Command
{
    /**
     * @var Environment
     */
    private $template;
    private $mailer;
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(
        EnfantRepository $enfantRepository,
        \Swift_Mailer $mailer,
        ParameterBagInterface $parameterBag,
        Environment $template
    ) {
        parent::__construct();
        $this->mailer = $mailer;
        $this->template = $template;
        $this->enfantRepository = $enfantRepository;
        $this->parameterBag = $parameterBag;
    }

    protected function configure()
    {
        $this
            ->setName('mercredi:filescheck')
            ->setDescription('Vérifie les pieces jointes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directorySante = $this->parameterBag->get('enfant_sante');
        $directoryInscription = $this->parameterBag->get('enfant_inscription');

        $enfants = $this->enfantRepository->findAll();
        $lost = [];
        foreach ($enfants as $enfant) {
            if ($enfant->getFicheName()) {
                $fileSante = $directorySante.'/'.$enfant->getId().'/'.$enfant->getFicheName();

                if (!is_readable($fileSante)) {
                    $lost[] = $fileSante.' id: => '.$enfant->getId();
                    $this->sendMail('pas de fichier sante', $enfant);
                }
            }

            if ($enfant->getFileName()) {
                $fileInsc = $directoryInscription.'/'.$enfant->getId().'/'.$enfant->getFileName();

                if (!is_readable($fileInsc)) {
                    $lost[] = $fileInsc;

                    $this->sendMail('pas de fichier inscription', $enfant);
                }
            }
        }
    }

    private function sendMail($sujet, Enfant $enfant)
    {
        $message = (new \Swift_Message('Admin pas '.$sujet))
            ->setFrom('jf@marche.be')
            ->setTo('webmaster@marche.be')
            ->setBody(
                $this->template->render(
                    'admin/default/mail.check.text.twig',
                    [
                        'enfant' => $enfant,
                    ]
                )
            );
        $this->mailer->send($message);
    }
}
