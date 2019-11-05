<?php

namespace AcMarche\Mercredi\Admin\Twig\Extension;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use AcMarche\Mercredi\Admin\Entity\Enfant;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class EnfantImage extends AbstractExtension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('enfantimage', [$this, 'photoEnfant']),
            new TwigFilter('animateurimage', [$this, 'photoAnimateur']),
        ];
    }

    public function photoEnfant(Enfant $enfant)
    {
        if ($enfant->getImageName()) {
            $directory = $this->container->getParameter('enfant_photo_web');
            $file = $directory.'/'.$enfant->getImageName();

            return $file;
        }
    }

    public function photoAnimateur(Animateur $animateur)
    {
        if ($animateur->getImageName()) {
            $directory = $this->container->getParameter('animateur_photo_web');
            $file = $directory.'/'.$animateur->getImageName();

            return $file;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'enfant_image';
    }
}
