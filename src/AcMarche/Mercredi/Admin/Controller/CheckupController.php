<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Paiement;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Paiement controller.
 *
 * @Route("/checkup")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class CheckupController extends AbstractController
{
    /**
     * @Route("/presences/nonpayes", name="checkup_presences_non_payer", methods={"GET","PUT"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function nonPayer(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $args = ['today' => true, 'result' => true, 'order' => 'enfant'];

        $presences = $em->getRepository(Presence::class)->getPresencesNonPayes($args);

        $args = ['today' => true, 'order' => 'enfant'];
        $presencesPlaines = $em->getRepository(PlainePresence::class)->getPresencesNonPayes($args);

        return $this->render('admin/checkup/non_payer.html.twig', [
            'presences' => $presences,
            'presencesPlaines' => $presencesPlaines,
        ]);
    }

    /**
     * @Route("/paiement", name="checkup_paiement", methods={"GET","PUT"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function paiement(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $args = ['cloture' => 0];

        $paiements = $em->getRepository(Paiement::class)->findBy($args);

        return $this->render('admin/checkup/paiement.html.twig', [
            'paiements' => $paiements,
        ]);
    }
}
