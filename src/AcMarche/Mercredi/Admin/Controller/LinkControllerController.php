<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Service\FormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @package AcMarche\Admin\Admin\Controller
 * @Route("/link")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class LinkControllerController extends AbstractController
{
    /**
     * @var FormService
     */
    private $formService;

    public function __construct(FormService $formService)
    {
        $this->formService = $formService;
    }

    /**
     * Detacher un tuteur.
     *
     * @Route("/detach/{id}", name="tuteur_detach", methods={"POST"})
     *
     */
    public function detach(Request $request, Enfant $enfant)
    {
        $form = $this->formService->createDetachForm($enfant);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $data = $form->getData();
            $tuteur_id = isset($data['tuteur_id']) ? $data['tuteur_id'] : 0;

            $tuteur = $em->getRepository(Tuteur::class)->find($tuteur_id);
            $enfantTuteur = $em->getRepository(EnfantTuteur::class)->findBy(['tuteur' => $tuteur, 'enfant' => $enfant]);

            if (!$enfantTuteur) {
                throw $this->createNotFoundException('Tuteur non trouvé.');
            }

            $em->remove($enfantTuteur);
            $em->flush();

            $this->addFlash('success', "Le parent a bien été détaché");
        }

        return $this->redirectToRoute('enfant_show', array('slugname' => $enfant->getSlugname()));
    }

    /**
     * Attach un tuteur a l'enfant.
     *
     * @Route("/attach/{id}", name="tuteur_attach", methods={"POST"})
     *
     */
    public function Attach(Request $request, Enfant $enfant)
    {
        $em = $this->getDoctrine()->getManager();

        $enfant_tuteur = new EnfantTuteur();

        $enfant_tuteur->setEnfant($enfant);

        $form = $this->formService->createAttachForm($enfant_tuteur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $tuteur = $enfant_tuteur->getTuteur();
            if (!$tuteur) {
                $this->addFlash('danger', "Le tuteur sélectionné n'a pas été trouvé");
            } else {
                $em->persist($enfant_tuteur);

                $em->flush();

                $this->addFlash('success', "Le tuteur a bien été associé");
            }
        }

        return $this->redirectToRoute(
            'enfant_show',
            array(
                'slugname' => $enfant->getSlugname(),
            )
        );
    }
}
