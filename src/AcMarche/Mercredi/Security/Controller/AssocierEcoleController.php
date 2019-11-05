<?php

namespace AcMarche\Mercredi\Security\Controller;

use AcMarche\Mercredi\Security\Entity\User;
use AcMarche\Mercredi\Security\Form\AssociateEcoleType;
use AcMarche\Mercredi\Security\Repository\UserRepository;
use AcMarche\Mercredi\Security\Service\Mailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * User controller.
 *
 * @Route("/security/associer/ecole")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class AssocierEcoleController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(
        UserRepository $userRepository,
        Mailer $mailer
    ) {
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/{id}", name="utilisateur_associate_ecole", methods={"GET","POST"})
     */
    public function associate(Request $request, User $user)
    {
        if (!$user->isEcole()) {
            $this->addFlash('danger', 'Le compte n\'a pas les droits d\'école');

            return $this->redirectToRoute('utilisateur_show', ['id' => $user->getId()]);
        }

        $form = $this->createForm(AssociateEcoleType::class, $user)
            ->add('submit', SubmitType::class, ['label' => 'Update']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->get('associate_ecole');
            $sendmail = $data['sendmail'];

            $this->userRepository->save();

            if ($sendmail) {
                $this->mailer->sendNewAccountToEcole($user);
                $this->addFlash('success', 'Un mail de bienvenue a été envoyé');
            }

            return $this->redirectToRoute('utilisateur_show', ['id' => $user->getId()]);
        }

        return $this->render(
            'security/utilisateur/associate_ecole.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }
}
