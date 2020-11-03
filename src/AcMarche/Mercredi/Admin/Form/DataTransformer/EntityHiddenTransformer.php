<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 28/08/18
 * Time: 10:04.
 */

namespace AcMarche\Mercredi\Admin\Form\DataTransformer;

use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * https://stackoverflow.com/questions/27094901/symfony-cant-we-have-a-hidden-entity-field#
 * use in form :
 *     $builder
 * ->get('tuteur')
 * ->addModelTransformer(
 * new EntityHiddenTransformer(
 * $this->getObjectManager(),
 * Tuteur::class,
 * 'id'
 * )
 * );.
 */

/**
 * Class EntityHiddenTransformer.
 *
 * @author  Francesco Casula <fra.casula@gmail.com>
 */
class EntityHiddenTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $objectManager;

    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $primaryKey;

    /**
     * EntityHiddenType constructor.
     *
     * @param string $className
     * @param string $primaryKey
     */
    public function __construct(EntityManagerInterface $objectManager, $className, $primaryKey)
    {
        $this->objectManager = $objectManager;
        $this->className = $className;
        $this->primaryKey = $primaryKey;
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * Transforms an object (entity) to a string (number).
     *
     * @param object|null $entity
     *
     * @return string
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return '';
        }

        $method = 'get'.ucfirst($this->getPrimaryKey());

        // Probably worth throwing an exception if the method doesn't exist
        // Note: you can always use reflection to get the PK even though there's no public getter for it

        return $entity->$method();
    }

    /**
     * Transforms a string (number) to an object (entity).
     *
     * @param string $identifier
     *
     * @return object|null
     *
     * @throws TransformationFailedException if object (entity) is not found
     */
    public function reverseTransform($identifier)
    {
        if (!$identifier) {
            return null;
        }

        $entity = $this->getObjectManager()
            ->getRepository($this->getClassName())
            ->find($identifier);

        if (null === $entity) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf('An entity with ID "%s" does not exist!', $identifier));
        }

        return $entity;
    }
}
