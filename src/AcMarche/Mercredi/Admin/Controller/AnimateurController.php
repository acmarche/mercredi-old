<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use AcMarche\Mercredi\Admin\Form\Animateur\AnimateurType;
use AcMarche\Mercredi\Admin\Repository\AnimateurRepository;
use AcMarche\Mercredi\Admin\Service\AnimateurFileHelper;
use AcMarche\Mercredi\Security\Service\Mailer;
use AcMarche\Mercredi\Security\Manager\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Admin\Form\Animateur\AnimateurEditType;
use AcMarche\Mercredi\Admin\Form\Search\SearchAnimateurType;
use AcMarche\Mercredi\Admin\Form\Animateur\AnimateurJoursType;
use AcMarche\Mercredi\Admin\Form\Animateur\AnimateurPlainesType;
use AcMarche\Mercredi\Plaine\Entity\AnimateurPlaine;

/**
 * Animateur controller.
 *
 * @Route("/animateur")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class AnimateurController extends AbstractController
{
    /**
     * @var AnimateurRepository
     */
    private $animateurRepository;
    /**
     * @var AnimateurFileHelper
     */
    private $animateurFileHelper;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(
        AnimateurRepository $animateurRepository,
        AnimateurFileHelper $animateurFileHelper,
        SessionInterface $session,
        UserManager $userManager,
        Mailer $mailer
    ) {
        $this->animateurRepository = $animateurRepository;
        $this->animateurFileHelper = $animateurFileHelper;
        $this->session = $session;
        $this->userManager = $userManager;
        $this->mailer = $mailer;
    }

    /**
     * Lists all Animateur entities.
     * @Route("/", name="admin_animateur", methods={"GET"})
     */
    public function index(Request $request)
    {
        $data = array();

        if ($this->session->has("animateur_search")) {
            $data = unserialize($this->session->get("animateur_search"));
        }

        $search_form = $this->createForm(
            SearchAnimateurType::class,
            $data,
            array(
                'action' => $this->generateUrl('admin_animateur'),
                'method' => 'GET',
            )
        );

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
            if ($search_form->get('raz')->isClicked()) {
                $this->session->remove("animateur_search");
                $this->addFlash('success', 'La recherche a bien été réinitialisée.');

                return $this->redirectToRoute('admin_animateur');
            }
        }

        $this->session->set('animateur_search', serialize($data));
        $animateurs = $this->animateurRepository->search($data);

        return $this->render(
            'admin/animateur/index.html.twig',
            array(
                'search_form' => $search_form->createView(),
                'animateurs' => $animateurs,
            )
        );
    }

    /**
     * Displays a form to create a new Animateur entity.
     *
     * @Route("/new", name="admin_animateur_new", methods={"GET","POST"})
     *
     */
    public function new(Request $request)
    {
        $animateur = new Animateur();

        $form = $form = $this->createForm(AnimateurType::class, $animateur)
            ->add('submit', SubmitType::class, array('label' => 'Create'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $animateur->setUserAdd($this->getUser());

            $this->animateurRepository->insert($animateur);

            if ($animateur->getEmail()) {
                $user = $this->userManager->newFromAnimateur($animateur);
                $password = $user->getPlainPassword();
                $this->mailer->sendNewAccountToAnimateur($user, $animateur, $password);
                $this->addFlash('success', "Un compte a été créé pour l'animateur");
            }

            $this->addFlash('success', "L'animateur a bien été ajouté");

            return $this->redirect(
                $this->generateUrl('admin_animateur_show', array('slugname' => $animateur->getSlugname()))
            );
        }

        return $this->render(
            'admin/animateur/new.html.twig',
            array(
                'animateur' => $animateur,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Animateur entity.
     *
     * @Route("/{slugname}", name="admin_animateur_show", methods={"GET"})
     *
     */
    public function show(Animateur $animateur)
    {
        $deleteForm = $this->createDeleteForm($animateur->getId());

        return $this->render(
            'admin/animateur/show.html.twig',
            array(
                'animateur' => $animateur,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Animateur entity.
     *
     * @Route("/{slugname}/edit", name="admin_animateur_edit", methods={"GET","POST"})
     *
     */
    public function edit(Request $request, Animateur $animateur)
    {
        $editForm = $form = $this->createForm(AnimateurEditType::class, $animateur)
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->animateurFileHelper->traitementFiles($animateur);
            $this->animateurRepository->save();

            $this->addFlash('success', "L'animateur a bien été modifié");

            return $this->redirect(
                $this->generateUrl('admin_animateur_show', array('slugname' => $animateur->getSlugname()))
            );
        }

        return $this->render(
            'admin/animateur/edit.html.twig',
            array(
                'animateur' => $animateur,
                'form' => $editForm->createView(),
            )
        );
    }

    /**
     * Deletes a Animateur entity.
     *
     * @Route("/{id}", name="admin_animateur_delete", methods={"DELETE"})
     *
     */
    public function delete(Request $request, Animateur $animateur)
    {
        $form = $this->createDeleteForm($animateur->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->animateurRepository->remove($animateur);

            $this->addFlash('success', "L'animateur a bien été supprimé");
        }

        return $this->redirect($this->generateUrl('admin_animateur'));
    }

    /**
     * @param $id
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_animateur_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                array(
                    'label' => 'Delete',
                    'attr' => array('class' => 'btn-danger'),
                )
            )
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Animateur entity.
     *
     * @Route("/{slugname}/jours", name="animateur_jours", methods={"GET","POST"})
     *
     */
    public function jours(Request $request, Animateur $animateur)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createJoursForm($animateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', "Les jours de présences ont bien été modifiés");

            return $this->redirect(
                $this->generateUrl('admin_animateur_show', array('slugname' => $animateur->getSlugname()))
            );
        }

        return $this->render(
            'admin/animateur/jours.html.twig',
            array(
                'animateur' => $animateur,
                'form' => $form->createView(),
            )
        );
    }

    private function createJoursForm(Animateur $entity)
    {
        $form = $this->createForm(
            AnimateurJoursType::class,
            $entity,
            array(
                'method' => 'POST',
            )
        );

        $form->add('submit', SubmitType::class, array('label' => 'Update'));

        return $form;
    }

    /**
     * Displays a form to edit an existing Animateur entity.
     *
     * @Route("/{slugname}/plaines", name="animateur_plaines", methods={"GET","POST"})
     * @Route("/{slugname}/plaines/{plaine_id}", name="animateur_plaine", methods={"GET","POST"})
     * @ParamConverter("plaine", class="AcMarche\Mercredi\Plaine\Entity\Plaine", options={"id" = "plaine_id"})
     *
     */
    public function plaines(Request $request, Animateur $animateur, Plaine $plaine = null)
    {
        $em = $this->getDoctrine()->getManager();

        $animateurPlaine = new AnimateurPlaine();
        $animateurPlaine->setAnimateur($animateur);

        if ($plaine) {
            $animateurPlaine->setPlaine($plaine);
        }

        $form = $this->createPlainesForm($animateurPlaine);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($animateurPlaine);
            $em->flush();

            $this->addFlash('success', "Les jours de garde ont bien été modifiés");

            return $this->redirect(
                $this->generateUrl('admin_animateur_show', array('slugname' => $animateur->getSlugname()))
            );
        }

        return $this->render(
            'admin/animateur/plaines.html.twig',
            array(
                'animateur' => $animateur,
                'plaine' => $plaine,
                'form' => $form->createView(),
            )
        );
    }

    private function createPlainesForm(AnimateurPlaine $entity)
    {
        $animateur = $entity->getAnimateur();
        $plaine = $entity->getPlaine();
        $args = array('slugname' => $animateur->getSlugname());

        if ($plaine) {
            $args['plaine_id'] = $plaine->getId();
        }

        $url = $this->generateUrl('animateur_plaines', $args);

        $form = $this->createForm(
            AnimateurPlainesType::class,
            $entity,
            array(
                'action' => $url,
                'method' => 'POST',
            )
        );

        $form->add('submit', SubmitType::class, array('label' => 'Update'));

        return $form;
    }


}
