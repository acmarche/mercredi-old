<?php

namespace AcMarche\Mercredi\Ecole\Controller;

use AcMarche\Mercredi\Admin\Entity\Ecole;
use AcMarche\Mercredi\Ecole\Form\EcoleType;
use AcMarche\Mercredi\Security\Entity\Group;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Ecole controller.
 *
 * @Route("/ecole")
 * @IsGranted("ROLE_MERCREDI_ECOLE")
 */
class EcoleController extends AbstractController
{
    /**
     * Lists all Ecole entities.
     *
     * @Route("/", name="ecole_ecole_index", methods={"GET"})
     *
     */
    public function index()
    {
        $user = $this->getUser();

        /**
         * @var Ecole[] $ecoles
         */
        $ecoles = $user->getEcoles();

        return $this->render(
            'ecole/ecole/index.html.twig',
            array(
                'ecoles' => $ecoles,
            )
        );
    }

    /**
     * Finds and displays a Ecole entity.
     *
     * @Route("/{id}", name="ecole_ecole_show", methods={"GET"})
     * @IsGranted("show",subject="ecole")
     */
    public function show(Ecole $ecole)
    {
        return $this->render(
            'ecole/ecole/show.html.twig',
            array(
                'ecole' => $ecole,
            )
        );
    }

    /**
     * Displays a form to edit an existing Ecole entity.
     *
     * @Route("/{id}/edit", name="ecole_ecole_edit", methods={"GET","POST"})
     * @IsGranted("edit",subject="ecole")
     */
    public function edit(Request $request, Ecole $ecole)
    {
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository(Group::class)->findOneBy(['name' => 'MERCREDI_ECOLE']);

        $editForm = $this->createForm(EcoleType::class, $ecole)
            ->add(
                'submit',
                SubmitType::class,
                ['label' => 'Update',]
            );

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            $this->addFlash('success', "L'école a bien été modifiée");

            return $this->redirectToRoute('ecole_ecole_show', ['id' => $ecole->getId()]);
        }

        return $this->render(
            'ecole/ecole/edit.html.twig',
            array(
                'ecole' => $ecole,
                'edit_form' => $editForm->createView(),
            )
        );
    }

}
