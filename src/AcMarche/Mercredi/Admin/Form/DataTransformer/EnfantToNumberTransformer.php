<?php
namespace AcMarche\Mercredi\Admin\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use AcMarche\Mercredi\Admin\Entity\Enfant;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EnfantToNumberTransformer implements DataTransformerInterface
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
     * @param  Enfant|null $enfant
     * @return string
     */
    public function transform($enfant)
    {
        if (null === $enfant) {
            return "";
        }

        return $enfant->getId();
    }

    /**
     * Transforms a string (number) to an object (enfant).
     *
     * @param  string $number
     *
     * @return Enfant|null
     *
     * @throws TransformationFailedException if object (enfant) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $enfant = $this->om
            ->getRepository(Enfant::class)
            ->find($number);

        if (null === $enfant) {
            throw new TransformationFailedException(sprintf(
                'An enfant with number "%s" does not exist!',
                $number
            ));
        }

        return $enfant;
    }
}
