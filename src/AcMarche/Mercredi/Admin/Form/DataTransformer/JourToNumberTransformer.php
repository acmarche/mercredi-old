<?php

namespace AcMarche\Mercredi\Admin\Form\DataTransformer;

use AcMarche\Mercredi\Admin\Entity\Jour;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class JourToNumberTransformer implements DataTransformerInterface
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
     * Transforms an object (jour) to a string (number).
     *
     * @param Jour|null $jour
     *
     * @return string
     */
    public function transform($jour)
    {
        if (null === $jour) {
            return '';
        }

        return $jour->getId();
    }

    /**
     * Transforms a string (number) to an object (jour).
     *
     * @param string $number
     *
     * @return Jour|null
     *
     * @throws TransformationFailedException if object (jour) is not found
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $jour = $this->om
            ->getRepository(Jour::class)
            ->find($number);

        if (null === $jour) {
            throw new TransformationFailedException(sprintf('An jour with number "%s" does not exist!', $number));
        }

        return $jour;
    }
}
