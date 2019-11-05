<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 18/01/19
 * Time: 22:15.
 */

namespace AcMarche\Mercredi\Api;

use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Repository\TuteurRepository;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;

class TuteurCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;

    public function __construct(TuteurRepository $tuteurRepository)
    {
        $this->tuteurRepository = $tuteurRepository;
    }

    /**
     * Retrieves a collection.
     *
     * @throws ResourceClassNotSupportedException
     *
     * @return array|\Traversable
     */
    public function getCollection(string $resourceClass, string $operationName = null)
    {
        return $this->tuteurRepository->findBy(['name' => 'Simon']);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        var_dump($resourceClass);
        exit();

        return Tuteur::class === $resourceClass;
    }
}
