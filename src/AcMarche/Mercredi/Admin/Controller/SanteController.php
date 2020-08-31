<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Parent\Form\SanteFicheType;
use AcMarche\Mercredi\Parent\Manager\SanteManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sante")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class SanteController extends AbstractController
{
    /**
     * @var SanteManager
     */
    private $santeManager;

    public function __construct(SanteManager $santeManager)
    {
        $this->santeManager = $santeManager;
    }

    /**
     * @Route("/show/{uuid}", name="admin_sante_show")
     */
    public function show(Enfant $enfant)
    {
        $questions = $this->santeManager->getAllQuestions();
        $santeFiche = $enfant->getSanteFiche();

        if ($santeFiche) {
            $questions = $this->santeManager->bindResponses($santeFiche);
        }

        $isComplete = $this->santeManager->isComplete($enfant);

        return $this->render(
            'admin/sante/show.html.twig',
            [
                'enfant' => $enfant,
                'fiche' => $santeFiche,
                'isComplete' => $isComplete,
                'questions' => $questions,
            ]
        );
    }

    /**
     * @Route("/edit/{uuid}", name="admin_sante_edit")
     * @IsGranted("edit", subject="enfant")
     */
    public function edit(Request $request, Enfant $enfant)
    {
        $santeFiche = $this->santeManager->getSanteFiche($enfant);
        $santeFiche->setQuestions($this->santeManager->bindResponses($santeFiche));

        $form = $this->createForm(SanteFicheType::class, $santeFiche)
            ->add('Enregistrer', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->santeManager->saveSanteFiche($santeFiche);

            /**
             * @var SanteQuestion[]
             */
            $questions = $data->getQuestions();

            foreach ($questions as $question) {
                $this->santeManager->handleReponse($santeFiche, $question);
            }

            $this->santeManager->Reponsesflush();

            $this->addFlash('success', 'La fiche de santé à bien été enregistrée');

            return $this->redirectToRoute('admin_sante_show', ['uuid' => $enfant->getUuid()]);
        }

        return $this->render(
            'admin/sante/edit.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }
}
