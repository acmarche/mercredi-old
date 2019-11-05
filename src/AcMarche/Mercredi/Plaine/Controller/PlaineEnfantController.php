<?php

namespace AcMarche\Mercredi\Plaine\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Service\FacturePlaine;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Plaine\Entity\PlaineEnfant;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * PlaineEnfant controller.
 *
 * @Route("/plaineenfant")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class PlaineEnfantController extends AbstractController
{
    /**
     * Affiche le detail de la presence dun enfant dans la plaine.
     *
     * @Route("/{plaine_slugname}/{enfant_slugname}", name="plainepresence_show_enfant", methods={"GET"})
     * @ParamConverter("plaine", class="AcMarche\Mercredi\Plaine\Entity\Plaine", options={"mapping": {"plaine_slugname": "slugname"}})
     * @ParamConverter("enfant", class="AcMarche\Mercredi\Admin\Entity\Enfant", options={"mapping": {"enfant_slugname": "slugname"}})
     */
    public function enfant(Enfant $enfant, Plaine $plaine, FacturePlaine $facturePlaine)
    {
        $em = $this->getDoctrine()->getManager();
        $enfant_id = $enfant->getId();
        $plaine_id = $plaine->getId();
        $args = ['enfant' => $enfant, 'plaine' => $plaine];

        $plaine_enfant = $em->getRepository(PlaineEnfant::class)->findOneBy($args);

        if (!$plaine_enfant) {
            throw $this->createNotFoundException('Unable to find plaineEnfant entity.');
        }

        $args = ['plaine_enfant_id' => $plaine_enfant];
        $presences = $em->getRepository(PlainePresence::class)->search($args);
        foreach ($presences as $presence) {
            $facturePlaine->handlePresence($presence);
        }

        $delete_form = $this->createRemoveEnfantForm($enfant_id, $plaine_id);

        return $this->render(
            'plaine/plaine_enfant/enfant.html.twig',
            [
                'enfant' => $enfant,
                'delete_form' => $delete_form->createView(),
                'plaine' => $plaine,
                'presences' => $presences,
            ]
        );
    }

    /**
     * Creates a form to delete a PlainePresence entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createRemoveEnfantForm($enfant_id, $plaine_id)
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'plainepresence_remove_enfant',
                    ['enfant_id' => $enfant_id, 'plaine_id' => $plaine_id]
                )
            )
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }

    /**
     * Supprime en enfant de la plaine.
     *
     * @Route("/{enfant_id}/{plaine_id}", name="plainepresence_remove_enfant", methods={"DELETE"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function removeEnfant(Request $request, $enfant_id, $plaine_id)
    {
        $form = $this->createRemoveEnfantForm($enfant_id, $plaine_id);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $plaine = $em->getRepository(Plaine::class)->find($plaine_id);

        if (!$plaine) {
            throw $this->createNotFoundException('Unable to find Plaine entity.');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $enfant = $em->getRepository(Enfant::class)->find($enfant_id);

            if (!$enfant) {
                throw $this->createNotFoundException('Unable to find Enfant entity.');
            }

            $args = ['enfant' => $enfant_id, 'plaine' => $plaine_id];
            $plaine_enfant = $em->getRepository(PlaineEnfant::class)->findOneBy($args);

            if (!$plaine_enfant) {
                throw $this->createNotFoundException('Unable to find PlaineEnfant entity.');
            }

            $em->remove($plaine_enfant);

            $em->flush();

            $this->addFlash('success', "L'enfant a bien été supprimé de la plaine");

            return $this->redirectToRoute('plaine_show', ['slugname' => $plaine->getSlugname()]);
        }

        return $this->redirectToRoute('plaine_show', ['slugname' => $plaine->getSlugname()]);
    }
}
