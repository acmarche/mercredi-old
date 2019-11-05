<?php

namespace AcMarche\Mercredi\Security\Controller;

use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Repository\PresenceRepository;
use AcMarche\Mercredi\Admin\Repository\TuteurRepository;
use AcMarche\Mercredi\Security\Entity\User;
use AcMarche\Mercredi\Security\Form\AssociateParentType;
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
 * @Route("/security/associer/parent")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class AssocierParentController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;

    public function __construct(
        UserRepository $userRepository,
        PresenceRepository $presenceRepository,
        TuteurRepository $tuteurRepository,
        Mailer $mailer
    ) {
        $this->userRepository = $userRepository;
        $this->presenceRepository = $presenceRepository;
        $this->mailer = $mailer;
        $this->tuteurRepository = $tuteurRepository;
    }

    /**
     * @Route("/{id}", name="utilisateur_associate_parent", methods={"GET","POST"})
     */
    public function associate(Request $request, User $user)
    {
        if (!$user->isParent()) {
            $this->addFlash('danger', 'Le compte n\'a pas les droits de parent');

            return $this->redirectToRoute('utilisateur_show', ['id' => $user->getId()]);
        }

        $tuteur = $this->tuteurRepository->findOneByEmail($user->getEmail());
        if ($tuteur) {
            $user->setTuteur($tuteur);
        }

        $form = $this->createForm(AssociateParentType::class, $user)
            ->add('submit', SubmitType::class, ['label' => 'Update']);

        $oldTuteur = $user->getTuteur();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('dissocier')->getData()) {
                if (null != $oldTuteur) {
                    $this->dissociateParent($oldTuteur);
                }
            } else {
                $tuteur = $form->getData()->getTuteur();
                if ($tuteur) {
                    $this->associerParent($user, $tuteur, $oldTuteur);
                    if ($request->request->get('Sendmail')) {
                        $this->mailer->sendNewAccountToParent($user, $tuteur);
                        $this->addFlash('success', 'Un mail de bienvenue a été envoyé');
                    }
                }
            }

            return $this->redirectToRoute('utilisateur_show', ['id' => $user->getId()]);
        }

        return $this->render(
            'security/utilisateur/associate_parent.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    private function dissociateParent(Tuteur $tuteur)
    {
        $tuteur->setUser(null);
        $this->userRepository->save();
        $this->addFlash('success', 'L\'utilisateur a bien été dissocié.');
    }

    private function associerParent(User $user, Tuteur $tuteur, Tuteur $oldTuteur = null)
    {
        /*
         * si deja associe sinon duplicate key
         */
        if ($oldTuteur) {
            $oldTuteur->setUser(null);
            $this->tuteurRepository->save();
        }
        $tuteur->setUser($user);
        $this->tuteurRepository->save();
        $this->addFlash('success', 'L\'utilisateur a bien été associé.');
        $this->mailer->sendNewAccountToParent($user, $tuteur);
    }
}
