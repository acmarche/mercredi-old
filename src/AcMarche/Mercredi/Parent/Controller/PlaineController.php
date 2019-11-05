<?php

namespace AcMarche\Mercredi\Parent\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Service\TuteurUtils;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Plaine\Entity\PlaineEnfant;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Plaine controller.
 *
 * @Route("/plaine")
 * @IsGranted("ROLE_MERCREDI_PARENT")
 */
class PlaineController extends AbstractController
{
    /**
     * @Route("/{plaine_slugname}/{enfant_uuid}", name="parent_plaine_show", methods={"GET"})
     * @ParamConverter("plaine", class="AcMarche\Mercredi\Plaine\Entity\Plaine", options={"mapping": {"plaine_slugname": "slugname"}})
     * @ParamConverter("enfant", class="AcMarche\Mercredi\Admin\Entity\Enfant", options={"mapping": {"enfant_uuid": "uuid"}})
     * @IsGranted("show", subject="enfant")
     */
    public function show(Enfant $enfant, Plaine $plaine)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $tuteur = TuteurUtils::getTuteurByUser($user);

        $plaine_id = $plaine->getId();
        $args = ['enfant' => $enfant->getId(), 'plaine' => $plaine_id];

        $plaine_enfant = $em->getRepository(PlaineEnfant::class)->findOneBy($args);

        if (!$plaine_enfant) {
            throw $this->createNotFoundException('Unable to find plaineEnfant entity.');
        }

        $args = ['plaine_enfant_id' => $plaine_enfant];
        $presences = $em->getRepository(PlainePresence::class)->search($args);

        return $this->render(
            'parent/plaine/show.html.twig',
            [
                'enfant' => $enfant,
                'plaine' => $plaine,
                'presences' => $presences,
                'entity' => $tuteur,
            ]
        );
    }
}
