<?php

namespace AcMarche\Mercredi\Security\Form\DataTransformer;

use AcMarche\Mercredi\Security\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UserToNumberTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param User|null $issue
     *
     * @return string
     */
    public function transform($issue)
    {
        if (null === $issue) {
            return '';
        }

        return $issue->getId();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param string $number
     *
     * @return User|null
     *
     * @throws TransformationFailedException if object (issue) is not found
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $issue = $this->om
            ->getRepository(User::class)
            ->findOneBy(['id' => $number]);

        if (null === $issue) {
            throw new TransformationFailedException(sprintf('An issue with number "%s" does not exist!', $number));
        }

        return $issue;
    }
}
