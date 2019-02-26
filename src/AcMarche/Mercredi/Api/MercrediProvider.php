<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 18/01/19
 * Time: 21:00
 */

namespace AcMarche\Mercredi\Api;


use AcMarche\Mercredi\Admin\Entity\Tuteur;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;

class MercrediProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * Retrieves an item.
     *
     * @param array|int|string $id
     *
     * @throws ResourceClassNotSupportedException
     *
     * @return object|null
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
         return $this->tuteurRepository->find($id);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Tuteur::class === $resourceClass;
    }
}