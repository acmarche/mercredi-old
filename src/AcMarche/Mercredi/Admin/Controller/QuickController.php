<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Form\Quick\QuickType;
use AcMarche\Mercredi\Admin\Repository\EnfantRepository;
use AcMarche\Mercredi\Admin\Repository\EnfantTuteurRepository;
use AcMarche\Mercredi\Admin\Repository\TuteurRepository;
use AcMarche\Mercredi\Commun\Utils\StringUtils;
use AcMarche\Mercredi\Security\Manager\UserManager;
use AcMarche\Mercredi\Security\Service\Mailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @package AcMarche\Mercredi\Admin\Controller
 * @Route("/quick")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class QuickController extends AbstractController
{
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var EnfantTuteurRepository
     */
    private $enfantTuteurRepository;
    /**
     * @var StringUtils
     */
    private $stringUtils;
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(
        TuteurRepository $tuteurRepository,
        EnfantRepository $enfantRepository,
        EnfantTuteurRepository $enfantTuteurRepository,
        UserManager $userManager,
        StringUtils $stringUtils,
        Mailer $mailer
    ) {
        $this->tuteurRepository = $tuteurRepository;
        $this->enfantRepository = $enfantRepository;
        $this->userManager = $userManager;
        $this->enfantTuteurRepository = $enfantTuteurRepository;
        $this->stringUtils = $stringUtils;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/", name="home_mercredi_quick")
     *
     */
    public function new(Request $request)
    {
        $currentUser = $this->getUser();
        $user = $password = null;
        $form = $this->createForm(QuickType::class)->add('Create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * @var Tuteur $tuteur
             */
            $tuteur = $form->getData()->getTuteur();
            /**
             * @var Enfant $enfant
             */
            $enfant = $form->getData()->getEnfant();

            $tuteur->setUserAdd($currentUser);
            $enfant->setUserAdd($currentUser);

            $this->tuteurRepository->insert($tuteur);
            $this->enfantRepository->insert($enfant);

            $enfantTuteur = new EnfantTuteur();
            $enfantTuteur->setEnfant($enfant);
            $enfantTuteur->setTuteur($tuteur);

            $this->enfantTuteurRepository->insert($enfantTuteur);

            if ($tuteur->getEmail()) {
                $user = $this->userManager->newFromTuteur($tuteur);
                $password = $user->getPlainPassword();
                $this->mailer->sendNewAccountToParent($user, $tuteur, $password);
                $this->addFlash('success', "Un compte a été créé pour le parent");
            }

            return $this->render(
                'admin/quick/finish.html.twig',
                [
                    'enfant' => $enfant,
                    'tuteur' => $tuteur,
                    'user' => $user,
                    'password' => $password,
                ]
            );

        }

        return $this->render('admin/quick/new.html.twig', ['form' => $form->createView()]);
    }
}
