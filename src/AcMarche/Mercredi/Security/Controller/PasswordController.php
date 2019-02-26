<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 27/12/16
 * Time: 13:26
 */

namespace AcMarche\Mercredi\Security\Controller;

use AcMarche\Mercredi\Commun\Utils\PasswordManager;
use AcMarche\Mercredi\Security\Entity\User;
use AcMarche\Mercredi\Security\Form\UserPasswordType;
use AcMarche\Mercredi\Security\Manager\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Password controller.
 *
 * @Route("/password")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class PasswordController extends AbstractController
{
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var PasswordManager
     */
    private $passwordManager;

    public function __construct(
        UserManager $userManager,
        PasswordManager $passwordManager,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->userManager = $userManager;
        $this->authorizationChecker = $authorizationChecker;
        $this->passwordManager = $passwordManager;
    }

    /**
     * @Route("/", name="user_change_password", methods={"GET","POST"})
     * @Route("/{id}", name="admin_user_change_password", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user = null)
    {
        $returnToUser = false;
        if ($user) {
            if (!$this->authorizationChecker->isGranted("ROLE_MERCREDI_ADMIN")) {
                return $this->createAccessDeniedException();
            } else {
                $returnToUser = true;
            }
        }

        if (!$user) {
            $user = $this->getUser();
        }

        $form = $this->createForm(UserPasswordType::class, $user)
            ->add('Update', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();

            $this->passwordManager->changePassword($user, $plainPassword);
            $this->userManager->save();

            $this->addFlash('success', 'Le mot de passe a bien été modifié.');

            if ($returnToUser) {
                return $this->redirectToRoute('utilisateur_show', ['id' => $user->getId()]);
            }

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'security/password/edit.html.twig',
            array(
                'user' => $user,
                'form' => $form->createView(),
            )
        );
    }
}
