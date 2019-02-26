<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Admin\Form\SanteQuestionType;
use AcMarche\Mercredi\Admin\Repository\SanteQuestionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/question")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class QuestionController extends AbstractController
{
    /**
     * @var SanteQuestionRepository
     */
    private $santeQuestionRepository;

    public function __construct(SanteQuestionRepository $santeQuestionRepository)
    {
        $this->santeQuestionRepository = $santeQuestionRepository;
    }

    /**
     *
     *
     * @Route("/", name="question", methods={"GET"})
     *
     */
    public function index()
    {
        $questions = $this->santeQuestionRepository->findAll();

        return $this->render(
            'admin/question/index.html.twig',
            array(
                'questions' => $questions,
            )
        );
    }

    /**
     *
     *
     * @Route("/new", name="question_new", methods={"GET","POST"})
     *
     *
     */
    public function new(Request $request)
    {
        $question = new SanteQuestion();
        $form = $form = $this->createForm(SanteQuestionType::class, $question)
            ->add('submit', SubmitType::class, array('label' => 'Create'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->santeQuestionRepository->insert($question);

            $this->addFlash('success', "La question a bien été ajoutée");

            return $this->redirectToRoute('question_show', ['id' => $question->getId()]);
        }

        return $this->render(
            'admin/question/new.html.twig',
            array(
                'question' => $question,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @Route("/{id}/show", name="question_show", methods={"GET"})
     */
    public function show(SanteQuestion $santeQuestion)
    {
        $deleteForm = $this->createDeleteForm($santeQuestion->getId());

        return $this->render(
            'admin/question/show.html.twig',
            array(
                'question' => $santeQuestion,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('question_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }

    /**
     * @Route("/{id}/edit", name="question_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, SanteQuestion $question)
    {
        $editForm = $form = $this->createForm(SanteQuestionType::class, $question)
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {

            $this->santeQuestionRepository->save();

            $this->addFlash('success', "La question a bien été mis à jour");

            return $this->redirectToRoute('question_show', ['id' => $question->getId()]);
        }

        return $this->render(
            'admin/question/edit.html.twig',
            array(
                'question' => $question,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     *
     * @Route("/{id}", name="question_delete", methods={"DELETE"})
     *
     */
    public function delete(Request $request, SanteQuestion $question)
    {
        $form = $this->createDeleteForm($question->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->santeQuestionRepository->remove($question);

            $this->addFlash('success', "La question a bien été supprimée");
        }

        return $this->redirectToRoute('question');
    }

    /**
     *
     * @Route("/trier", name="question_trier", methods={"GET","POST"})
     *
     */
    public function trier(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $questions = $request->request->get("questions");
            if (is_array($questions)) {
                foreach ($questions as $position => $questionId) {
                    $santeQuestion = $this->santeQuestionRepository->find($questionId);
                    if ($santeQuestion) {
                        $santeQuestion->setDisplayOrder($position);
                    }
                }
                $this->santeQuestionRepository->save();

                return new Response('<div class="alert alert-success">Tri enregistré</div>');
            }

            return new Response('<div class="alert alert-error">Erreur</div>');
        }

        $questions = $this->santeQuestionRepository->findAll();

        return $this->render(
            'admin/question/trier.html.twig',
            array(
                'questions' => $questions,
            )
        );
    }
}
