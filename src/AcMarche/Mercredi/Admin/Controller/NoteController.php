<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Note;
use AcMarche\Mercredi\Admin\Form\NoteType;
use AcMarche\Mercredi\Admin\Repository\NoteRepository;
use AcMarche\Mercredi\Admin\Service\MailerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Note controller.
 *
 * @Route("/note")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class NoteController extends AbstractController
{
    /**
     * @var NoteRepository
     */
    private $noteRepository;
    /**
     * @var MailerService
     */
    private $mailerService;

    public function __construct(NoteRepository $noteRepository, MailerService $mailerService)
    {
        $this->noteRepository = $noteRepository;
        $this->mailerService = $mailerService;
    }

    /**
     * Lists all Note entities.
     *
     * @Route("/{slugname}", name="note", methods={"GET"})
     */
    public function index(Enfant $enfant)
    {
        $notes = $this->noteRepository->search(
            ['enfant' => $enfant]
        );

        return $this->render(
            'admin/note/index.html.twig',
            [
                'enfant' => $enfant,
                'notes' => $notes,
            ]
        );
    }

    /**
     * Displays a form to create a new Note entity.
     *
     * @Route("/new/{slugname}", name="note_new", methods={"GET","POST"})
     */
    public function new(Enfant $enfant, Request $request)
    {
        $note = new Note();
        $note->setEnfant($enfant);

        $form = $this->createForm(NoteType::class, $note)
            ->add('submit', SubmitType::class, ['label' => 'Create']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $note->setUserAdd($user);

            $this->noteRepository->insert($note);

            $this->addFlash('success', 'La note a bien été ajoutée');

            return $this->redirectToRoute('note_show', ['id' => $note->getId()]);
        }

        return $this->render(
            'admin/note/new.html.twig',
            [
                'entity' => $note,
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Note entity.
     *
     * @Route("/detail/{id}", name="note_show", methods={"GET"})
     */
    public function show(Note $note)
    {
        $deleteForm = $this->createDeleteForm($note->getId());

        return $this->render(
            'admin/note/show.html.twig',
            [
                'entity' => $note,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to delete a Note entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('note_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Clôturer', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Note entity.
     *
     * @Route("/{id}/edit", name="note_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Note $note)
    {
        $editForm = $this->createForm(NoteType::class, $note)
            ->add('submit', SubmitType::class, ['label' => 'Update']);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            $this->noteRepository->save();

            $this->addFlash('success', 'La note a bien été mis à jour');

            return $this->redirectToRoute('note_show', ['id' => $note->getId()]);
        }

        return $this->render(
            'admin/note/edit.html.twig',
            [
                'entity' => $note,
                'edit_form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Deletes a Note entity.
     *
     * @Route("/{id}", name="note_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Note $note)
    {
        $form = $this->createDeleteForm($note->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $enfant = $note->getEnfant();

            $this->noteRepository->delete($note);

            $this->mailerService->sendNote($note);

            $this->addFlash('success', 'La note a bien été supprimée');
        }

        return $this->redirectToRoute('enfant_show', ['slugname' => $enfant->getSlugname()]);
    }
}
