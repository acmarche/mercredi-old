<?php

namespace AcMarche\Mercredi\Security\Controller;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use AcMarche\Mercredi\Admin\Repository\AnimateurRepository;
use AcMarche\Mercredi\Security\Entity\User;
use AcMarche\Mercredi\Security\Form\AssociateAnimateurType;
use AcMarche\Mercredi\Security\Repository\UserRepository;
use AcMarche\Mercredi\Security\Service\Mailer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * User controller.
 *
 * @Route("/security/associer/animateur")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class AssocierAnimateurController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var AnimateurRepository
     */
    private $animateurRepository;

    public function __construct(
        UserRepository $userRepository,
        AnimateurRepository $animateurRepository,
        Mailer $mailer
    ) {
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->animateurRepository = $animateurRepository;
    }

    /**
     *
     * @Route("/{id}", name="utilisateur_associate_animateur", methods={"GET","POST"})
     *
     */
    public function associate(Request $request, User $user)
    {
        if (!$user->isAnimateur()) {
            $this->addFlash('danger', 'Le compte n\'a pas les droits de animateur');

            return $this->redirectToRoute('utilisateur_show', ['id' => $user->getId()]);
        }

        $animateur = $this->animateurRepository->findOneBy(['email' => $user->getEmail()]);
        if ($animateur) {
            $user->setAnimateur($animateur);
        }

        $form = $this->createForm(AssociateAnimateurType::class, $user)
            ->add('submit', SubmitType::class, ['label' => 'Update']);

        $oldAnimateur = $user->getAnimateur();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('dissocier')->getData()) {
                if ($oldAnimateur != null) {
                    $this->dissociateAnimateur($oldAnimateur);
                }
            } else {
                $animateur = $form->getData()->getAnimateur();
                if ($animateur) {
                    $this->associerAnimateur($user, $animateur, $oldAnimateur);
                }
            }

            return $this->redirectToRoute('utilisateur_show', ['id' => $user->getId()]);
        }

        return $this->render(
            'security/utilisateur/associate_animateur.html.twig',
            array(
                'user' => $user,
                'form' => $form->createView(),
            )
        );
    }

    private function dissociateAnimateur(Animateur $animateur)
    {
        $animateur->setUser(null);
        $this->userRepository->save();
        $this->addFlash('success', 'L\'utilisateur a bien été dissocié.');
    }

    private function associerAnimateur(User $user, Animateur $animateur, Animateur $oldAnimateur = null)
    {
        /**
         * si deja associe sinon duplicate key
         */
        if ($oldAnimateur) {
            $oldAnimateur->setUser(null);
            $this->animateurRepository->save();
        }
        $animateur->setUser($user);
        $this->animateurRepository->save();
        $this->addFlash('success', 'L\'utilisateur a bien été associé.');
        $this->mailer->sendNewAccountToAnimateur($user, $animateur);
    }
}
