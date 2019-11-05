<?php

namespace AcMarche\Mercredi\Security\Controller;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Form\Search\SearchUtilisateurType;
use AcMarche\Mercredi\Admin\Repository\PresenceRepository;
use AcMarche\Mercredi\Security\Entity\User;
use AcMarche\Mercredi\Security\Form\UtilisateurEditType;
use AcMarche\Mercredi\Security\Form\UtilisateurType;
use AcMarche\Mercredi\Security\Manager\UserManager;
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
 * @Route("/security/utilisateurs")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class UtilisateurController extends AbstractController
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
     * @var UserManager
     */
    private $userManager;
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(
        UserRepository $userRepository,
        UserManager $userManager,
        PresenceRepository $presenceRepository,
        Mailer $mailer
    ) {
        $this->userRepository = $userRepository;
        $this->presenceRepository = $presenceRepository;
        $this->userManager = $userManager;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/", name="utilisateur")
     */
    public function index(Request $request)
    {
        $session = $request->getSession();

        $key = 'utilisateur_search';
        $data = $utilisateurs = [];
        $search = false;

        if ($session->has($key)) {
            $data = unserialize($session->get($key));
            $search = true;
        }

        $search_form = $this->createForm(
            SearchUtilisateurType::class,
            $data,
            [
                'method' => 'GET',
            ]
        );

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
            $search = true;
            if ($search_form->get('raz')->isClicked()) {
                $session->remove($key);
                $this->addFlash('success', 'La recherche a bien été réinitialisée.');

                return $this->redirectToRoute('utilisateur');
            }
            $session->set($key, serialize($data));
        }

        if ($search) {
            $utilisateurs = $this->userRepository->search($data);
        } else {
            $utilisateurs = $this->userRepository->search([]);
        }

        return $this->render(
            'security/utilisateur/index.html.twig',
            [
                'search_form' => $search_form->createView(),
                'users' => $utilisateurs,
            ]
        );
    }

    /**
     * @Route("/new", name="utilisateur_new", methods={"GET","POST"})
     */
    public function new(Request $request)
    {
        $user = new User();

        $form = $this->createForm(UtilisateurType::class, $user)
            ->add('create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->insert($user);
            $this->addFlash('success', "L'utilisateur a bien été ajouté");

            $this->addFlash('info', sprintf('Mot de passe généré: %s:', $user->getPlainPassword()));

            $this->mailer->sendNewAccountToUser($user);

            $this->addFlash('info', "Un email contenant le nom d'utilisateur et le mot de passe a été envoyé");

            return $this->redirectToRoute('utilisateur_show', ['id' => $user->getId()]);
        }

        return $this->render(
            'security/utilisateur/new.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/new/fromtuteur/{id}", name="utilisateur_new_tuteur", methods={"GET","POST"})
     */
    public function newFromTuteur(Request $request, Tuteur $tuteur)
    {
        $user = $this->userManager->getInstance($tuteur->getEmail());
        $this->userManager->populateFromObject($user, $tuteur);

        $form = $this->createForm(UtilisateurType::class, $user)
            ->add('create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userManager->newFromTuteur($tuteur, $user);
            $this->mailer->sendNewAccountToParent($user, $tuteur, $user->getPlainPassword());
            $this->addFlash('success', 'Un mail de bienvenue a été envoyé');

            $this->addFlash('success', "L'utilisateur a bien été ajouté");

            return $this->redirectToRoute('utilisateur_show', ['id' => $user->getId()]);
        }

        return $this->render(
            'security/utilisateur/new.html.twig',
            [
                'user' => $user,
                'entity' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/new/fromanimateur/{id}", name="utilisateur_new_animateur", methods={"GET","POST"})
     */
    public function newFromAnimateur(Request $request, Animateur $animateur)
    {
        $user = $this->userManager->getInstance($animateur->getEmail());
        $this->userManager->populateFromObject($user, $animateur);

        $form = $this->createForm(UtilisateurType::class, $user)
            ->add('create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->newFromAnimateur($animateur, $user);
            $this->mailer->sendNewAccountToAnimateur($user, $animateur, $user->getPlainPassword());
            $this->addFlash('success', 'Un mail de bienvenue a été envoyé');
            $this->addFlash('success', "L'utilisateur a bien été ajouté");

            return $this->redirectToRoute('utilisateur_show', ['id' => $user->getId()]);
        }

        return $this->render(
            'security/utilisateur/new.html.twig',
            [
                'user' => $user,
                'entity' => $animateur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="utilisateur_show", methods={"GET"})
     */
    public function show(User $user)
    {
        $deleteForm = $this->createDeleteForm($user->getId());
        $presences = $this->presenceRepository->search(['user' => $user]);

        return $this->render(
            'security/utilisateur/show.html.twig',
            [
                'user' => $user,
                'presences' => $presences,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Abonnement entity.
     *
     * @Route("/{id}/edit", name="utilisateur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user)
    {
        $form = $this->createForm(UtilisateurEditType::class, $user)
            ->add('Update', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->save();

            $this->addFlash('success', 'L\'utilisateur a bien été modifié.');

            return $this->redirectToRoute('utilisateur_show', ['id' => $user->getId()]);
        }

        return $this->render(
            'security/utilisateur/edit.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Deletes a user entity.
     *
     * @Route("/{id}", name="utilisateur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $presences = $this->presenceRepository->search(['user' => $user]);

            if (count($presences) > 0) {
                $this->addFlash(
                    'error',
                    "L'utilisateur ne peut être supprimé car il y a des présences encodées à son nom"
                );

                return $this->redirectToRoute('utilisateur');
            }

            $this->userManager->delete($user);

            $this->addFlash('success', 'Le user a bien été supprimé.');
        }

        return $this->redirectToRoute('utilisateur');
    }

    /**
     * Creates a form to delete a Batiment entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('utilisateur_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }
}
