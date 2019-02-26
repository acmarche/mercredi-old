<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\CaisseAllocation;
use AcMarche\Mercredi\Admin\Form\CaisseAllocationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * CaisseAllocation controller.
 *
 * @Route("/caisseallocation")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class CaisseAllocationController extends AbstractController
{

    /**
     * Lists all CaisseAllocation entities.
     *
     * @Route("/", name="caisseallocation", methods={"GET"})
     *
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(CaisseAllocation::class)->search();

        return $this->render('admin/caisse_allocation/index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a form to create a CaisseAllocation entity.
     *
     * @param CaisseAllocation $entity The entity
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createCreateForm(CaisseAllocation $entity)
    {
        $form = $this->createForm(CaisseAllocationType::class, $entity, array(
            'action' => $this->generateUrl('caisseallocation_new'),
            'method' => 'POST',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new CaisseAllocation entity.
     *
     * @Route("/new", name="caisseallocation_new", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     *
     */
    public function new(Request $request)
    {
        $entity = new CaisseAllocation();
        $form = $this->createCreateForm($entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            $entity->setUserAdd($user);

            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', "La caisse d'allocation a bien été ajoutée");

            return $this->redirectToRoute('caisseallocation');
        }

        return $this->render('admin/caisse_allocation/new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a CaisseAllocation entity.
     *
     * @Route("/{slugname}", name="caisseallocation_show", methods={"GET"})
     *
     */
    public function show(CaisseAllocation $caisse = null)
    {
        $deleteForm = $this->createDeleteForm($caisse->getId());

        return $this->render('admin/caisse_allocation/show.html.twig', array(
            'entity' => $caisse,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a CaisseAllocation entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return FormInterface
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('caisseallocation_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
                        ->getForm()
        ;
    }

    /**
     * Displays a form to edit an existing CaisseAllocation entity.
     *
     * @Route("/{id}/edit", name="caisseallocation_edit", methods={"GET","PUT"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     *
     */
    public function edit(Request $request, CaisseAllocation $caisse)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createEditForm($caisse);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            $this->addFlash('success', "La caisse d'allocation a bien été modifiée");

            return $this->redirectToRoute('caisseallocation');
        }

        return $this->render('admin/caisse_allocation/edit.html.twig', array(
            'entity' => $caisse,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a CaisseAllocation entity.
     *
     * @param CaisseAllocation $entity The entity
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createEditForm(CaisseAllocation $entity)
    {
        $form = $this->createForm(CaisseAllocationType::class, $entity, array(
            'action' => $this->generateUrl('caisseallocation_edit', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Update'));

        return $form;
    }

    /**
     * Deletes a CaisseAllocation entity.
     *
     * @Route("/{id}", name="caisseallocation_delete", methods={"DELETE"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function delete(Request $request, CaisseAllocation $caisse)
    {
        $form = $this->createDeleteForm($caisse->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($caisse);
            $em->flush();

            $this->addFlash('success', "La caisse d'allocation a bien été supprimée");
        }

        return $this->redirectToRoute('caisseallocation');
    }
}
