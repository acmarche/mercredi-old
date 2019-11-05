<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 17/01/18
 * Time: 10:03.
 */

namespace AcMarche\Mercredi\Logger\Entity;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class RequestProcessor.
 */
class RequestProcessor
{
    /**
     * @var RequestStack
     */
    protected $request;

    /**
     * @var TokenStorageInterface
     */
    protected $token;

    /**
     * RequestProcessor constructor.
     *
     * @param  $token
     */
    public function __construct(RequestStack $request, TokenStorageInterface $tokenStorage)
    {
        $this->request = $request;
        $this->token = $tokenStorage;
    }

    /**
     * @return array
     */
    public function processRecord(array $record)
    {
        $req = $this->request->getCurrentRequest();
        $token = $this->token->getToken();
        $user = $token ? $token->getUser() : 'null';

        $record['extra']['client_ip'] = $req->getClientIp();
        $record['extra']['client_port'] = $req->getPort();
        $record['extra']['uri'] = $req->getUri();
        $record['extra']['query_string'] = $req->getQueryString();
        $record['extra']['method'] = $req->getMethod();
        $record['extra']['request'] = $req->request->all();
        $record['extra']['user'] = $user;

        return $record;
    }
}
