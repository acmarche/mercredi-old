<?php

namespace AcMarche\Mercredi\Front\Controller;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Events\EnfantEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DownloadController.
 *
 * @Route("/download")
 */
class DownloadController extends AbstractController
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/enfant/{uuid}/{type}", name="download_enfant")
     *
     * @IsGranted("show", subject="enfant")
     */
    public function enfant(Enfant $enfant, $type)
    {
        $this->eventDispatcher->dispatch(new EnfantEvent($enfant, $type), EnfantEvent::ENFANT_DOWNLOAD);

        $file = false;
        switch ($type) {
            case 'sante':
                $directory = $this->getParameter('enfant_sante');
                $file = $directory.'/'.$enfant->getId().'/'.$enfant->getFicheName();
                break;
            case 'inscription':
                $directory = $this->getParameter('enfant_inscription');
                $file = $directory.'/'.$enfant->getId().'/'.$enfant->getFileName();
                break;
            default:
                break;
        }

        if (is_readable($file)) {
            return new BinaryFileResponse($file);
        }

        return new Response('Fichier non trouvé: '.$file);
    }

    /**
     * @Route("/animateur/{slugname}/{type}", name="download_animateur")
     *
     * @IsGranted({"ROLE_MERCREDI_READ","ROLE_MERCREDI_ANIMATEUR"})
     */
    public function animateur(Animateur $animateur, $type)
    {
        $file = false;
        switch ($type) {
            case 'cv':
                $directory = $this->getParameter('animateur_cv');
                $file = $directory.'/'.$animateur->getId().'/'.$animateur->getFileName();
                break;
            case 'casier':
                $directory = $this->getParameter('animateur_casier');
                $file = $directory.'/'.$animateur->getId().'/'.$animateur->getCasierName();
                break;
            case 'diplome':
                $directory = $this->getParameter('animateur_diplome');
                $file = $directory.'/'.$animateur->getId().'/'.$animateur->getDiplomeName();
                break;
            case 'certificat':
                $directory = $this->getParameter('animateur_certificat');
                $file = $directory.'/'.$animateur->getId().'/'.$animateur->getCertificatName();
                break;
            default:
                break;
        }
        if (is_readable($file)) {
            return new BinaryFileResponse($file);
        }

        return new Response('Fichier non trouvé : '.$file);
    }
}
