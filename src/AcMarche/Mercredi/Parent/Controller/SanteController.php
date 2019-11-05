<?php

namespace AcMarche\Mercredi\Parent\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Admin\Service\MailerService;
use AcMarche\Mercredi\Parent\Form\SanteFicheType;
use AcMarche\Mercredi\Parent\Manager\SanteManager;
use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sante")
 */
class SanteController extends AbstractController
{
    /**
     * @var SanteManager
     */
    private $santeManager;
    /**
     * @var Pdf
     */
    private $pdf;
    /**
     * @var MailerService
     */
    private $mailerService;

    public function __construct(SanteManager $santeManager, Pdf $pdf, MailerService $mailerService)
    {
        $this->santeManager = $santeManager;
        $this->pdf = $pdf;
        $this->mailerService = $mailerService;
    }

    /**
     * @Route("/show/{uuid}", name="parent_sante_show")
     * @IsGranted("show", subject="enfant")
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
            'parent/sante/show.html.twig',
            [
                'enfant' => $enfant,
                'fiche' => $santeFiche,
                'questions' => $questions,
                'isComplete' => $isComplete,
            ]
        );
    }

    /**
     * @Route("/edit/{uuid}", name="parent_sante_edit")
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

            $this->mailerService->sendFicheSanteUpdate($santeFiche, $this->getUser());

            $this->addFlash('success', 'La fiche de santé à bien été enregistrée');

            return $this->redirectToRoute('parent_sante_show', ['uuid' => $enfant->getUuid()]);
        }

        return $this->render(
            'parent/sante/edit.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/pdf/{uuid}", name="parent_sante_pdf")
     * @IsGranted("edit", subject="enfant")
     */
    public function pdf(Enfant $enfant)
    {
        $santeFiche = $this->santeManager->getSanteFiche($enfant);
        $santeFiche->setQuestions($this->santeManager->bindResponses($santeFiche));

        if ($santeFiche) {
            $questions = $this->santeManager->bindResponses($santeFiche);
        }

        $isComplete = $this->santeManager->isComplete($enfant);

        $html = $this->renderView('parent/sante/pdf/head.html.twig');
        $html .= $this->renderView(
            'parent/sante/pdf/pdf.html.twig',
            [
                'enfant' => $enfant,
                'fiche' => $santeFiche,
                'questions' => $questions,
                'isComplete' => $isComplete,
            ]
        );
        $html .= $this->renderView('parent/sante/pdf/foot.html.twig');

        //debug
        // return new Response($html);

        return new Response(
            $this->pdf->getOutputFromHtml($html),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="sante-'.$enfant->getSlugname().'.pdf"',
            ]
        );
    }
}
