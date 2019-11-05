<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 18/01/19
 * Time: 20:55.
 */

namespace AcMarche\Mercredi\Api;

use ApiPlatform\Core\Exception\RuntimeException;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class AdminContextBuilder.
 */
class AdminContextBuilder implements SerializerContextBuilderInterface
{
    /**
     * @var SerializerContextBuilderInterface
     */
    private $decorated;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(
        SerializerContextBuilderInterface $decorated,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Creates a serialization context from a Request.
     *
     * @throws RuntimeException
     */
    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            if ($normalization) {
                // Add admin:write for normalization.
                $context['groups'][] = 'admin:read';
            } else {
                // Add admin:read contexts for denormalization.
                $context['groups'][] = 'admin:write';
            }
        }

        return $context;
    }
}
