<?php

namespace AcMarche\Mercredi\Security\Controller;

use AcMarche\Mercredi\Security\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class SecurityController extends AbstractController
{
    private $tokenManager;
    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(UserManager $userManager, CsrfTokenManagerInterface $tokenManager = null)
    {
        $this->tokenManager = $tokenManager;
        $this->userManager = $userManager;
    }

    /**
     * @param Request $request
     * @Route("/login", name="mercredi_login")
     * @return Response
     */
    public function login(Request $request)
    {
        /** @var $session Session */
        $session = $request->getSession();

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        $csrfToken = $this->tokenManager
            ? $this->tokenManager->getToken('authenticate')->getValue()
            : null;

        return $this->renderLogin(
            array(
                'last_username' => $lastUsername,
                'error' => $error,
                'csrf_token' => $csrfToken,
            )
        );
    }

    /**
     * @Route("/login_check", name="mercredi_login_check")
     */
    public function check()
    {
        throw new \RuntimeException(
            'You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.'
        );
    }

    /**
     * @Route("/logout", name="mercredi_logout")
     */
    public function logout()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return Response
     */
    protected function renderLogin(array $data)
    {
        return $this->render('security/login.html.twig', $data);
    }

    public function renderMenu()
    {
        $user = $this->getUser();
        $roles = $this->userManager->getRolesForProfile($user);

        return $this->render('security/default/_menu.html.twig', ['roles' => $roles]);
    }
}
