<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Repository\EnfantRepository;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Upgrade controller.
 *
 * @Route("/upgrade")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class UpgradeController extends AbstractController
{
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;

    public function __construct(EnfantRepository $enfantRepository)
    {
        $this->enfantRepository = $enfantRepository;
    }

    /**
     * @Route("/", name="upgrade_ecole", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function index(Request $request)
    {
        $enfants = $this->enfantRepository->findBy(['archive' => 0], ['nom' => 'ASC']);

        $form = $this->createFormBuilder()
            ->add('submit', SubmitType::class, ['label' => "Valider le changement d'année scolaire"])
            ->getForm();

        $form->handleRequest($request);

        $scolaires = array_values(ScolaireService::getAnneesScolaires());
        $count = count($scolaires);
        $last = $scolaires[$count - 1];

        foreach ($enfants as $enfant) {
            $scolaire = $enfant->getAnneeScolaire();

            if ($scolaire === $last) {
                $enfant->setNewScolaire('Archive');
                $enfant->setArchive(true);
            } else {
                $key = array_search($scolaire, $scolaires);
                ++$key;
                $enfant->setNewScolaire($scolaires[$key]);
                if ($form->isSubmitted() && $form->isValid()) {
                    $enfant->setAnneeScolaire($scolaires[$key]);
                    $this->enfantRepository->save();
                }
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', "Le changement d'année scolaire a bien été effectué");

            return $this->redirectToRoute('upgrade_ecole');
        }

        return $this->render(
            'admin/upgrade/index.html.twig',
            [
                'enfants' => $enfants,
                'form' => $form->createView(),
            ]
        );
    }
}
