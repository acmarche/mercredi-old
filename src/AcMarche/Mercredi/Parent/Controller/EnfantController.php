<?php

namespace AcMarche\Mercredi\Parent\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Repository\EnfantRepository;
use AcMarche\Mercredi\Admin\Repository\EnfantTuteurRepository;
use AcMarche\Mercredi\Admin\Repository\PresenceRepository;
use AcMarche\Mercredi\Admin\Service\EnfantUtils;
use AcMarche\Mercredi\Admin\Service\Facture;
use AcMarche\Mercredi\Admin\Service\FileUploader;
use AcMarche\Mercredi\Admin\Service\FraterieService;
use AcMarche\Mercredi\Admin\Service\TuteurUtils;
use AcMarche\Mercredi\Commun\Utils\SortUtils;
use AcMarche\Mercredi\Parent\Form\EnfantEditType;
use AcMarche\Mercredi\Plaine\Repository\PlaineEnfantRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Enfant controller.
 *
 * @Route("/enfants")
 * @IsGranted("ROLE_MERCREDI_PARENT")
 */
class EnfantController extends AbstractController
{
    /**
     * @var EnfantTuteurRepository
     */
    private $enfantTuteurRepository;
    /**
     * @var FileUploader
     */
    private $fileUploader;
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var Facture
     */
    private $facture;
    /**
     * @var EnfantUtils
     */
    private $enfantUtils;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var PlaineEnfantRepository
     */
    private $plaineEnfantRepository;
    /**
     * @var FraterieService
     */
    private $fraterieService;
    /**
     * @var SortUtils
     */
    private $sortUtils;

    public function __construct(
        EnfantTuteurRepository $enfantTuteurRepository,
        EnfantRepository $enfantRepository,
        PresenceRepository $presenceRepository,
        PlaineEnfantRepository $plaineEnfantRepository,
        FileUploader $fileUploader,
        Facture $facture,
        SortUtils $sortUtils,
        EnfantUtils $enfantUtils,
        FraterieService $fraterieService
    ) {
        $this->enfantTuteurRepository = $enfantTuteurRepository;
        $this->fileUploader = $fileUploader;
        $this->enfantRepository = $enfantRepository;
        $this->facture = $facture;
        $this->enfantUtils = $enfantUtils;
        $this->presenceRepository = $presenceRepository;
        $this->plaineEnfantRepository = $plaineEnfantRepository;
        $this->fraterieService = $fraterieService;
        $this->sortUtils = $sortUtils;
    }

    /**
     * @Route("/", name="parent_enfants")
     * @IsGranted("index_tuteur")
     */
    public function index()
    {
        $user = $this->getUser();
        $tuteur = TuteurUtils::getTuteurByUser($user);

        $enfants = $this->enfantUtils->getEnfantsByTuteur($tuteur);

        return $this->render(
            'parent/enfant/index.html.twig',
            [
                'enfants' => $enfants,
            ]
        );
    }

    /**
     * @Route("/enfant/{uuid}", name="parent_enfant_show", methods={"GET"})
     * @IsGranted("show", subject="enfant")
     */
    public function show(Enfant $enfant)
    {
        $user = $this->getUser();
        $tuteur = TuteurUtils::getTuteurByUser($user);

        $enfantTuteur = $this->enfantTuteurRepository->findOneBy(
            [
                'tuteur' => $tuteur,
                'enfant' => $enfant,
            ]
        );

        if (!$enfantTuteur) {
            $this->addFlash('danger', 'Une erreur est survenue');
            $this->redirectToRoute('parent_enfants');
        }

        $presences = $this->presenceRepository->getByEnfantTuteur($enfantTuteur);
        $fratries = $this->enfantRepository->getFratriesBy($enfant, $tuteur);

        foreach ($presences as $presence) {
            $this->facture->handlePresence($presence, $fratries);
            $enfantTuteur->addPresence($presence);
        }

        $presences2 = $this->sortUtils->sortPresence($enfantTuteur);
        $enfantTuteur->addPresencesByMonth($presences2);

        $year = date('Y') + 1;
        $years = range(2014, $year);

        $allFratries = $this->fraterieService->getFratrie($enfant);

        $plaines = $this->plaineEnfantRepository->search(['enfant_id' => $enfant->getId()]);

        return $this->render(
            'parent/enfant/show.html.twig',
            [
                'enfant' => $enfant,
                'enfantTuteur' => $enfantTuteur,
                'plaines' => $plaines,
                'fratries' => $allFratries,
                'years' => $years,
            ]
        );
    }

    /**
     * @Route("/edit/{uuid}", name="parent_enfant_edit")
     * @IsGranted("edit", subject="enfant")
     */
    public function edit(Request $request, Enfant $enfant)
    {
        if (0 == count($enfant->getAccompagnateurs())) {
            $enfant->addAccompagnateur(' ');
        }

        $form = $this->createForm(EnfantEditType::class, $enfant)
            ->add('Update', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newList = $form->get('accompagnateurs')->getData();
            $enfant->setAccompagnateurs($newList);
            $this->fileUploader->traitementFiles($enfant);

            $this->enfantRepository->save();

            $this->addFlash('success', 'La fiche a bien été modifiée');

            return $this->redirectToRoute('parent_enfant_show', ['uuid' => $enfant->getUuid()]);
        }

        return $this->render(
            'parent/enfant/edit.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }
}
