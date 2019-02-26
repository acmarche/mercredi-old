<?php

namespace AcMarche\Mercredi\Animateur\Controller;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use AcMarche\Mercredi\Admin\Form\Animateur\AnimateurEditType;
use AcMarche\Mercredi\Admin\Repository\AnimateurRepository;
use AcMarche\Mercredi\Admin\Service\AnimateurFileHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @IsGranted("ROLE_MERCREDI_ANIMATEUR")
 * @Route("/animateur")
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

    public function __construct(AnimateurRepository $animateurRepository, AnimateurFileHelper $animateurFileHelper)
    {
        $this->animateurRepository = $animateurRepository;
        $this->animateurFileHelper = $animateurFileHelper;
    }

    /**
     * Finds and displays a Animateur entity.
     *
     * @Route("/show", name="animateur_show", methods={"GET"})
     *
     */
    public function show()
    {
        $animateur = $this->getAnimateur();
        if (!$animateur) {
            $this->addFlash('danger', 'Aucune fiche relié à votre compte');

            return $this->redirectToRoute('home_animateur');
        }

        return $this->render(
            'animateur/animateur/show.html.twig',
            array(
                'animateur' => $animateur,
            )
        );
    }

    /**
     * Displays a form to edit an existing Animateur entity.
     *
     * @Route("/edit", name="animateur_edit", methods={"GET","POST"})
     *
     */
    public function edit(Request $request)
    {
        $animateur = $this->getAnimateur();
        if (!$animateur) {
            $this->addFlash('danger', 'Aucune fiche relié à votre compte');

            return $this->redirectToRoute('home_animateur');
        }

        $editForm = $form = $this->createForm(AnimateurEditType::class, $animateur)
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->animateurFileHelper->traitementFiles($animateur);
            $this->animateurRepository->save();

            $this->addFlash('success', "L'animateur a bien été modifié");

            return $this->redirect(
                $this->generateUrl('animateur_show', array('slugname' => $animateur->getSlugname()))
            );
        }

        return $this->render(
            'animateur/animateur/edit.html.twig',
            array(
                'animateur' => $animateur,
                'form' => $editForm->createView(),
            )
        );
    }

    private function getAnimateur(): ?Animateur
    {
        return $this->animateurRepository->findOneBy(['user' => $this->getUser()]);
    }

}
