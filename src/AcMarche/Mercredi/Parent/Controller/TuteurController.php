<?php

namespace AcMarche\Mercredi\Parent\Controller;

use AcMarche\Mercredi\Admin\Service\MailerService;
use AcMarche\Mercredi\Admin\Service\TuteurUtils;
use AcMarche\Mercredi\Parent\Form\CoordonneesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tuteur controller.
 *
 * @Route("/tuteur")
 * @IsGranted("ROLE_MERCREDI_PARENT")
 */
class TuteurController extends AbstractController
{
    /**
     * @var MailerService
     */
    private $mailerService;

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    /**
     * @Route("/coordonnees", name="parent_coordonnees", methods={"GET"})
     * @IsGranted("index_tuteur")
     */
    public function coordonnees()
    {
        $user = $this->getUser();
        $tuteur = TuteurUtils::getTuteurByUser($user);

        return $this->render('parent/tuteur/coordonnees.html.twig', array('tuteur' => $tuteur));
    }

    /**
     * @Route("/coordonnees/edit", name="parent_coordonnees_edit", methods={"GET","POST"})
     * @IsGranted("index_tuteur")
     */
    public function edit(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $tuteur = TuteurUtils::getTuteurByUser($user);

        $oldTuteur = clone $tuteur;

        $form = $this->createForm(CoordonneesType::class, $tuteur)
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', "Vos coordonnées ont bien été modifiées");

            $this->mailerService->sendContactTuteurChange($tuteur, $oldTuteur, $user->getEmail());

            return $this->redirectToRoute('parent_coordonnees');
        }

        return $this->render(
            'parent/tuteur/edit.html.twig',
            array(
                'entity' => $tuteur,
                'form' => $form->createView(),
            )
        );
    }


    /**
     * @Route("/paiements", name="parent_paiements", methods={"GET"})
     * @IsGranted("index_tuteur")
     */
    public function paiements()
    {
        $user = $this->getUser();
        $tuteur = TuteurUtils::getTuteurByUser($user);

        $paiements = $tuteur->getPaiements();

        return $this->render(
            'parent/tuteur/paiements.html.twig',
            array(
                'paiements' => $paiements,
                'entity' => $tuteur,
            )
        );
    }


}
