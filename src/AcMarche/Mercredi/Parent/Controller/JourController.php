<?php

namespace AcMarche\Mercredi\Parent\Controller;

use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Repository\JourRepository;
use AcMarche\Mercredi\Admin\Service\MailerService;
use AcMarche\Mercredi\Commun\Utils\DateService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/presence")
 * @IsGranted("ROLE_MERCREDI_PARENT")
 */
class JourController extends AbstractController
{
    /**
     * @var MailerService
     */
    private $mailerService;
    /**
     * @var JourRepository
     */
    private $jourRepository;
    /**
     * @var DateService
     */
    private $dateService;

    public function __construct(
        JourRepository $jourRepository,
        MailerService $mailerService,
        DateService $dateService
    ) {
        $this->mailerService = $mailerService;
        $this->jourRepository = $jourRepository;
        $this->dateService = $dateService;
    }

    /**
     * @Route("/{id}", name="parent_presence_show", methods={"GET"})
     * @IsGranted("show", subject="presence")
     */
    public function show(Presence $presence)
    {
        $form = $this->createDeleteForm($presence->getId());

        return $this->render(
            'parent/jour/show.html.twig',
            [
                'entity' => $presence,
                'delete_form' => $form->createView(),
            ]
        );
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parent_presence_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }

    /**
     * Deletes a Presence entity.
     *
     * @Route("/{id}", name="parent_presence_delete", methods={"DELETE"})
     *
     * @IsGranted("delete", subject="presence")
     */
    public function delete(Request $request, Presence $presence)
    {
        $form = $this->createDeleteForm($presence->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $jour = $presence->getJour();

            if (!$this->dateService->checkDate($jour->getDateJour())) {
                $content = $this->renderView('parent/jour/error_delais_delete.txt.twig', ['jour' => $jour]);
                $this->addFlash('danger', $content);

                return $this->redirectToRoute('parent_enfants');
            }

            $presenceCopy = clone $presence;

            $em = $this->getDoctrine()->getManager();
            $em->remove($presence);
            $em->flush();

            $this->mailerService->sendPresenceDeletedByParent($presenceCopy, $this->getUser());

            $this->addFlash('success', 'La présence a bien été effacée');
        }

        return $this->redirectToRoute('parent_enfants');
    }
}
