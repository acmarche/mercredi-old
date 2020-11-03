<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Message;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Form\MessageType;
use AcMarche\Mercredi\Admin\Form\Search\SearchMessageType;
use AcMarche\Mercredi\Admin\Manager\MessageManager;
use AcMarche\Mercredi\Admin\Repository\EnfantRepository;
use AcMarche\Mercredi\Admin\Repository\EnfantTuteurRepository;
use AcMarche\Mercredi\Admin\Repository\JourRepository;
use AcMarche\Mercredi\Admin\Repository\PresenceRepository;
use AcMarche\Mercredi\Admin\Repository\TuteurRepository;
use AcMarche\Mercredi\Admin\Service\EnfantUtils;
use AcMarche\Mercredi\Admin\Service\MailerService;
use AcMarche\Mercredi\Admin\Service\PresenceService;
use AcMarche\Mercredi\Admin\Service\TuteurUtils;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use AcMarche\Mercredi\Commun\Utils\SortUtils;
use AcMarche\Mercredi\Plaine\Repository\PlaineEnfantRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineJourRepository;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Message controller.
 *
 * @Route("/message")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class MessageController extends AbstractController
{
    private const KEY_SESSION = 'message_emails';
    public const KEY_GROUP_SESSION = 'message_emails_from_groupe';

    /**
     * @var MailerService
     */
    private $mailerService;
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;
    /**
     * @var PlaineEnfantRepository
     */
    private $plaineEnfantRepository;
    /**
     * @var TuteurUtils
     */
    private $tuteurUtils;
    /**
     * @var PlainePresenceRepository
     */
    private $plainePresenceRepository;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var PresenceService
     */
    private $presenceService;
    /**
     * @var SortUtils
     */
    private $sortUtils;
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var EnfantTuteurRepository
     */
    private $enfantTuteurRepository;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var ScolaireService
     */
    private $scolaireService;
    /**
     * @var EnfantUtils
     */
    private $enfantUtils;
    /**
     * @var MessageManager
     */
    private $messageManager;
    /**
     * @var JourRepository
     */
    private $jourRepository;
    /**
     * @var PlaineJourRepository
     */
    private $plaineJourRepository;

    public function __construct(
        MessageManager $messageManager,
        TuteurRepository $tuteurRepository,
        EnfantRepository $enfantRepository,
        EnfantTuteurRepository $enfantTuteurRepository,
        TuteurUtils $tuteurUtils,
        PlaineEnfantRepository $plaineEnfantRepository,
        PlainePresenceRepository $plainePresenceRepository,
        PresenceRepository $presenceRepository,
        PresenceService $presenceService,
        SortUtils $sortUtils,
        SessionInterface $session,
        ScolaireService $scolaireService,
        EnfantUtils $enfantUtils,
        JourRepository $jourRepository,
        PlaineJourRepository $plaineJourRepository
    ) {
        $this->tuteurRepository = $tuteurRepository;
        $this->plaineEnfantRepository = $plaineEnfantRepository;
        $this->tuteurUtils = $tuteurUtils;
        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->presenceRepository = $presenceRepository;
        $this->presenceService = $presenceService;
        $this->sortUtils = $sortUtils;
        $this->enfantRepository = $enfantRepository;
        $this->enfantTuteurRepository = $enfantTuteurRepository;
        $this->session = $session;
        $this->scolaireService = $scolaireService;
        $this->enfantUtils = $enfantUtils;
        $this->messageManager = $messageManager;
        $this->jourRepository = $jourRepository;
        $this->plaineJourRepository = $plaineJourRepository;
    }

    /**
     * @Route("/", name="message", methods={"GET"})
     */
    public function index(Request $request)
    {
        $tuteurs = [];
        $search_form = $this->createForm(SearchMessageType::class, [], ['method' => 'GET']);
        $search_form->handleRequest($request);

        if ($search_form->isSubmitted()) {
            if ($search_form->isValid()) {
                $data = $search_form->getData();
                $ecole = $data['ecole'];
                $plaine = $data['plaine'];
                $jour = $data['jour'];

                if ($jour) {
                    $tuteurs = $this->presenceService->getTuteursByPrences(
                        $this->presenceRepository->findBy(['jour' => $jour])
                    );
                }

                if ($ecole) {
                    $enfants = $this->enfantRepository->findBy(['ecole' => $ecole, 'archive' => 0]);
                    $tuteurs = $this->enfantTuteurRepository->getTuteursByEnfants($enfants);
                }

                if ($plaine) {
                    $plaineEnfants = $this->plaineEnfantRepository->findBy(['plaine' => $plaine]);

                    foreach ($plaineEnfants as $plaineEnfant) {
                        $tuteurs = array_merge(
                            $tuteurs,
                            $this->presenceService->getTuteursByPrences(
                                $this->plainePresenceRepository->findBy(['plaine_enfant' => $plaineEnfant])
                            )
                        );
                    }
                    $tuteurs = array_unique($tuteurs);
                }
            }
        } else {
            //$enfants = $this->enfantRepository->quickSearchActif([]);
            //$tuteurs = $this->enfantUtils->extractTuteurs($enfants);
            $relations = $this->enfantTuteurRepository->findTuteursActifs();
            $tuteurs = TuteurUtils::extractTuteurs($relations);
        }

        $emails = $this->tuteurUtils->getEmails($tuteurs);
        $tuteursWithOutEmails = $this->tuteurUtils->filterTuteursWithOutEmails($tuteurs);

        $this->session->set(self::KEY_SESSION, $emails);

        return $this->render(
            'admin/message/index.html.twig',
            [
                'form' => $search_form->createView(),
                'emails' => $emails,
                'tuteurs' => $this->sortUtils->sortObjectsByName($tuteurs),
                'tuteursWithOutEmails' => $tuteursWithOutEmails,
            ]
        );
    }

    /**
     * Displays a form to create a new Plaine entity.
     *
     * @Route("/new", name="message_new", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function new(Request $request)
    {
        $message = $this->messageManager->newInstance();

        $form = $form = $this->createForm(MessageType::class, $message)
            ->add('submit', SubmitType::class, ['label' => 'Envoyer le message']);

        $destinataires = $this->session->get(self::KEY_SESSION, []);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->messageManager->handleMessage($message, $destinataires);

            $this->addFlash('success', 'Le message a bien été envoyé');

            $this->session->remove(self::KEY_SESSION);

            return $this->redirectToRoute('message');
        }

        $from = $this->getParameter('enfance_email_from');

        return $this->render(
            'admin/message/new.html.twig',
            [
                'emailuser' => $this->getUser()->getEmail(),
                'form' => $form->createView(),
                'destinataires' => $destinataires,
                'from' => $from,
            ]
        );
    }

    /**
     * @Route("/new/groupescolaire/{groupe}", name="message_new_groupescolaire", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function newFromGroupe(Request $request, $groupe)
    {
        $message = $this->messageManager->newInstance();

        $form = $form = $this->createForm(MessageType::class, $message)
            ->add('submit', SubmitType::class, ['label' => 'Envoyer le message']);

        $args = $this->session->get(self::KEY_GROUP_SESSION, []);
        if (count($args) < 1) {
            return $this->redirectToRoute('presence');
        }

        $type = $args['type'];
        unset($args['type']);

        if ('mercredi' == $type) {
            $presences = $this->presenceRepository->search($args);
        }

        if ('plaine' == $type) {
            $presences = $this->plainePresenceRepository->search($args);
        }

        $groupes = $this->scolaireService->groupPresences($presences, $type);
        $enfants = isset($groupes[$groupe]) ? $groupes[$groupe] : [];
        $tuteurs = $this->enfantUtils->extractTuteurs($enfants);

        $destinataires = $this->tuteurUtils->getEmails($tuteurs);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->messageManager->handleMessage($message, $destinataires);

            $this->addFlash('success', 'Le message a bien été envoyé');

            $this->session->remove(self::KEY_GROUP_SESSION);

            return $this->redirectToRoute('message');
        }

        return $this->render(
            'admin/message/new.html.twig',
            [
                'emailuser' => $this->getUser()->getEmail(),
                'form' => $form->createView(),
                'destinataires' => $destinataires,
            ]
        );
    }

    /**
     * @Route("/new/jour/{id}/{type}", name="message_new_jour", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function newFromJour(Request $request, int $id, string $type)
    {
        $message = $this->messageManager->newInstance();

        $form = $form = $this->createForm(MessageType::class, $message)
            ->add('submit', SubmitType::class, ['label' => 'Envoyer le message']);

        $args = $this->session->get(self::KEY_GROUP_SESSION, []);
        if (count($args) < 1) {
            return $this->redirectToRoute('presence');
        }

        if ('mercredi' === $type) {
            $jour = $this->jourRepository->find($id);
            $presences = $this->presenceRepository->findBy(['jour' => $jour]);
        }

        if ('plaine' === $type) {
            $jour = $this->plaineJourRepository->find($id);
            $presences = $this->plainePresenceRepository->findBy(['jour' => $jour]);
        }

        $tuteurs = $this->presenceService->getTuteursByPrences($presences);
        $destinataires = $this->tuteurUtils->getEmails($tuteurs);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->messageManager->handleMessage($message, $destinataires);

            $this->addFlash('success', 'Le message a bien été envoyé');

            return $this->redirectToRoute('message');
        }

        return $this->render(
            'admin/message/new.html.twig',
            [
                'emailuser' => $this->getUser()->getEmail(),
                'form' => $form->createView(),
                'destinataires' => $destinataires,
            ]
        );
    }

    /**
     * Displays a form to create a new Plaine entity.
     *
     * @Route("/new/tuteur/{id}", name="message_new_tuteur", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function newFromTuteur(Request $request, Tuteur $tuteur)
    {
        $message = $this->messageManager->newInstance();

        $form = $form = $this->createForm(MessageType::class, $message)
            ->add('submit', SubmitType::class, ['label' => 'Envoyer le message']);

        $destinataires = $this->tuteurUtils->getEmails([$tuteur]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->messageManager->handleMessage($message, $destinataires);

            $this->addFlash('success', 'Le message a bien été envoyé');

            return $this->redirectToRoute('message');
        }

        return $this->render(
            'admin/message/new.html.twig',
            [
                'emailuser' => $this->getUser()->getEmail(),
                'form' => $form->createView(),
                'destinataires' => $destinataires,
            ]
        );
    }

    /**
     * Pour envoie de test.
     *
     * @Route("/ajax/test", name="message_test", methods={"POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function test(Request $request)
    {
        $email = $request->request->get('email');
        $sujet = $request->request->get('sujet');
        $body = $request->request->get('body');
        $file = $request->request->get('file');
        $response = new Response();
        if (!$sujet && !$body) {
            $response->setContent('<div class="alert alert-danger">Veuillez remplir tous les champs</div>');

            return $response;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response->setContent('<div class="alert alert-danger">Email invalide</div>');

            return $response;
        } else {
            $this->messageManager->sendTest($sujet, $body, $email);
            $response->setContent('<div class="alert alert-success">Mail de test envoyé</div>');
        }

        return $response;
    }

    /**
     * @Route("/archive", name="message_archive", methods={"GET"})
     */
    public function archive()
    {
        $messages = $this->messageManager->getAll();

        return $this->render(
            'admin/message/archive.html.twig',
            [
                'messages' => $messages,
            ]
        );
    }

    /**
     * @Route("/show/{id}", name="message_show", methods={"GET"})
     */
    public function show(Message $message)
    {
        return $this->render(
            'admin/message/show.html.twig',
            [
                'message' => $message,
            ]
        );
    }
}
