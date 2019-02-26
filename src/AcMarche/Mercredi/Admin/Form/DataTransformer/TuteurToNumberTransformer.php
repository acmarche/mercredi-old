<?php

namespace AcMarche\Mercredi\Admin\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TuteurToNumberTransformer implements DataTransformerInterface
{

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (enfant) to a string (number).
     *
     * @param  Tuteur|null $tuteur
     * @return string
     */
    public function transform($tuteur)
    {
        if (null === $tuteur) {
            return "";
        }

        return $tuteur->getId();
    }

    /**
     * Transforms a string (number) to an object (enfant).
     *
     * @param  string $number
     *
     * @return Tuteur|null
     *
     * @throws TransformationFailedException if object (enfant) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $tuteur = $this->om
            ->getRepository(Tuteur::class)
            ->find($number);

        if (null === $tuteur) {
            throw new TransformationFailedException(sprintf(
                'An Tuteur with number "%s" does not exist!',
                $number
            ));
        }

        return $tuteur;
    }
}
