<?php

namespace AcMarche\Mercredi\Security\Controller;

use AcMarche\Mercredi\Commun\Utils\PasswordManager;
use AcMarche\Mercredi\Security\Form\LostPasswordType;
use AcMarche\Mercredi\Security\Form\ResettingFormType;
use AcMarche\Mercredi\Security\MailerSecurity;
use AcMarche\Mercredi\Security\Manager\UserManager;
use AcMarche\Mercredi\Security\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegisterController
 * @package AcMarche\Admin\Security\Controller
 * @Route("reset/password")
 */
class ResettingController extends AbstractController
{
    /**
     * @var MailerSecurity
     */
    private $mailerSecurity;
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var PasswordManager
     */
    private $passwordManager;

    public function __construct(
        MailerSecurity $mailerSecurity,
        UserRepository $userRepository,
        UserManager $userManager,
        PasswordManager $passwordManager
    ) {
        $this->mailerSecurity = $mailerSecurity;
        $this->userManager = $userManager;
        $this->userRepository = $userRepository;
        $this->passwordManager = $passwordManager;
    }

    /**
     * @param Request $request
     * @Route("/request", name="mercredi_password_lost", methods={"GET", "POST"})
     * @return Response
     * @throws \Exception
     */
    public function request(Request $request)
    {
        $form = $this->createForm(LostPasswordType::class)
            ->add('submit', SubmitType::class, ['label' => 'Demander un nouveau mot de passe']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->userRepository->findOneBy(['email' => $form->getData()->getEmailRequest()]);
            if (!$user) {
                $this->addFlash('warning', 'Aucun utilisateur trouvé');

                return $this->redirectToRoute('mercredi_password_lost');
            }

            $token = $this->generateToken();
            $user->setConfirmationToken($token);
            $this->userManager->save();
            $this->mailerSecurity->sendRequestNewPassword($user);

            return $this->redirectToRoute('mercredi_password_confirmation');
        }

        return $this->render(
            'security/resetting/request.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/confirmation", name="mercredi_password_confirmation", methods={"GET"})
     * @return Response
     */
    public function requestConfirmed()
    {
        return $this->render(
            'security/resetting/confirmed.html.twig'
        );
    }

    /**
     * Reset user password.
     * @Route("/{token}", name="mercredi_password_reset", methods={"GET","POST"})
     * @param Request $request
     * @param string $token
     *
     * @return Response
     */
    public function reset(Request $request, $token)
    {
        $user = $this->userRepository->findOneBy(['confirmationToken' => $token]);

        if (null === $user) {
            $this->addFlash('warning', 'Jeton non trouvé');

            return $this->redirectToRoute('mercredi_login');
        }

        $form = $this->createForm(ResettingFormType::class, $user)
            ->add('submit', SubmitType::class, ['label' => 'Valider']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->passwordManager->changePassword($user, $form->getData()->getPlainPassword());
            $user->setConfirmationToken(null);
            $this->userManager->save();

            $this->addFlash('success', 'Votre mot de passe a bien été changé');

            return $this->redirectToRoute('mercredi_login');
        }

        return $this->render(
            'security/resetting/reset.html.twig',
            array(
                'token' => $token,
                'form' => $form->createView(),
            )
        );
    }


    /**
     * @return string
     * @throws \Exception
     */
    public function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
