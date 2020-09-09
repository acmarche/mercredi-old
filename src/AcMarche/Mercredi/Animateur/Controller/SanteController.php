<?php

namespace AcMarche\Mercredi\Animateur\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Parent\Manager\SanteManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sante")
 * @IsGranted("ROLE_MERCREDI_ANIMATEUR")
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
     * @Route("/show/{uuid}", name="animateur_sante_show")
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
            'animateur/sante/show.html.twig',
            [
                'enfant' => $enfant,
                'fiche' => $santeFiche,
                'isComplete' => $isComplete,
                'questions' => $questions,
            ]
        );
    }
}
