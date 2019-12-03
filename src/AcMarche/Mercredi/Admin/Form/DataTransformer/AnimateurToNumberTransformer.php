<?php

namespace AcMarche\Mercredi\Admin\Form\DataTransformer;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class AnimateurToNumberTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    public function __construct(EntityManagerInterface $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (animateur) to a string (number).
     *
     * @param Animateur|null $animateur
     *
     * @return string
     */
    public function transform($animateur)
    {
        if (null === $animateur) {
            return '';
        }

        return $animateur->getId();
    }

    /**
     * Transforms a string (number) to an object (animateur).
     *
     * @param string $number
     *
     * @return Animateur|null
     *
     * @throws TransformationFailedException if object (animateur) is not found
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $animateur = $this->om
            ->getRepository(Animateur::class)
            ->find($number);

        if (null === $animateur) {
            throw new TransformationFailedException(sprintf('An animateur with number "%s" does not exist!', $number));
        }

        return $animateur;
    }
}
