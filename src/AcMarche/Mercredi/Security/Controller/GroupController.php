<?php

namespace AcMarche\Mercredi\Security\Controller;

use AcMarche\Mercredi\Security\Entity\Group;
use AcMarche\Mercredi\Security\Form\GroupeType;
use AcMarche\Mercredi\Security\Repository\GroupRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * User controller.
 *
 * @Route("/security/groupe")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class GroupController extends AbstractController
{
    /**
     *
     * @Route("/", name="groupe_index")
     *
     */
    public function index(GroupRepository $groupRepository)
    {
        $groups = $groupRepository->findAll();

        return $this->render(
            'security/groupe/index.html.twig',
            array(
                'groupes' => $groups,
            )
        );
    }


    /**
     * Finds and displays a User entity.
     *
     * @Route("/{name}", name="groupe_show", methods={"GET"})
     *
     */
    public function show(Group $group)
    {
        return $this->render(
            'security/groupe/show.html.twig',
            array(
                'entity' => $group,
            )
        );
    }

    /**
     * Displays a form to edit an existing Abonnement entity.
     *
     * @Route("/{name}/edit", name="groupe_edit", methods={"GET","PUT"})
     */
    public function edit(Request $request, Group $groupe)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $form = $this->createForm(
            GroupeType::class,
            $groupe
        );

        $form->add('submit', SubmitType::class, array('label' => 'Update'));

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Le groupe a bien été modifié.');

            return $this->redirectToRoute('groupe_index');
        }

        return $this->render(
            'security/groupe/edit.html.twig',
            array(
                'entity' => $groupe,
                'edit_form' => $editForm->createView(),
            )
        );
    }
}
