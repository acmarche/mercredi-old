<?php

namespace AcMarche\Mercredi\Plaine\Form\DataTransformer;

use AcMarche\Mercredi\Plaine\Entity\Plaine;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PlaineToNumberTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $om;

    public function __construct(EntityManagerInterface $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (plaine) to a string (number).
     *
     * @param Plaine|null $plaine
     *
     * @return string
     */
    public function transform($plaine)
    {
        if (null === $plaine) {
            return '';
        }

        return $plaine->getId();
    }

    /**
     * Transforms a string (number) to an object (plaine).
     *
     * @param string $number
     *
     * @return Plaine|null
     *
     * @throws TransformationFailedException if object (plaine) is not found
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $plaine = $this->om
            ->getRepository(Plaine::class)
            ->find($number);

        if (null === $plaine) {
            throw new TransformationFailedException(sprintf('An plaine with number "%s" does not exist!', $number));
        }

        return $plaine;
    }
}
