<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Ecole;
use AcMarche\Mercredi\Admin\Form\EcoleType;
use AcMarche\Mercredi\Security\Entity\Group;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Ecole controller.
 *
 * @Route("/ecole")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class EcoleController extends AbstractController
{
    /**
     * Lists all Ecole entities.
     *
     * @Route("/", name="ecole", methods={"GET"})
     *
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $ecoles = $em->getRepository(Ecole::class)->findAll();

        return $this->render('admin/ecole/index.html.twig', array(
            'ecoles' => $ecoles,
        ));
    }

    /**
     * Displays a form to create a new Ecole entity.
     *
     * @Route("/new", name="ecole_new", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     *
     */
    public function new(Request $request)
    {
        $ecole = new Ecole();
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository(Group::class)->findOneBy(['name' => 'MERCREDI_ECOLE']);

        $form = $this->createForm(
            EcoleType::class,
            $ecole,
            [
                'group' => $group,
            ]
        )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Create',
                ]
            );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ecole);
            $em->flush();

            $this->addFlash('success', "L'école a bien été ajoutée");

            return $this->redirectToRoute('ecole');
        }

        return $this->render('admin/ecole/new.html.twig', array(
            'ecole' => $ecole,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Ecole entity.
     *
     * @Route("/{id}", name="ecole_show", methods={"GET"})
     *
     */
    public function show(Ecole $ecole)
    {
        $deleteForm = $this->createDeleteForm($ecole->getId());

        return $this->render('admin/ecole/show.html.twig', array(
            'ecole' => $ecole,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Ecole entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return FormInterface
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ecole_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Ecole entity.
     *
     * @Route("/{id}/edit", name="ecole_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     *
     */
    public function edit(Request $request, Ecole $ecole)
    {
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository(Group::class)->findOneBy(['name' => 'MERCREDI_ECOLE']);

        $editForm = $this->createForm(
            EcoleType::class,
            $ecole,
            [
                'group' => $group,
            ]
        )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Update',
                ]
            );

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            $this->addFlash('success', "L'école a bien été modifiée");

            return $this->redirectToRoute('ecole');
        }

        return $this->render('admin/ecole/edit.html.twig', array(
            'ecole' => $ecole,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Ecole entity.
     *
     * @Route("/{id}", name="ecole_delete", methods={"DELETE"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function delete(Request $request, Ecole $ecole)
    {
        $form = $this->createDeleteForm($ecole->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($ecole);
            $em->flush();

            $this->addFlash('success', "La caisse d'allocation a bien été supprimée");
        }

        return $this->redirectToRoute('ecole');
    }
}
