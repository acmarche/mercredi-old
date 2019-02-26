<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 17/01/19
 * Time: 10:38
 */

namespace AcMarche\Mercredi\Api\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Repository\EcoleRepository;
use AcMarche\Mercredi\Admin\Repository\EnfantRepository;
use AcMarche\Mercredi\Admin\Repository\EnfantTuteurRepository;
use AcMarche\Mercredi\Admin\Repository\JourRepository;
use AcMarche\Mercredi\Admin\Repository\PaiementRepository;
use AcMarche\Mercredi\Admin\Repository\SanteFicheRepository;
use AcMarche\Mercredi\Admin\Repository\SanteQuestionRepository;
use AcMarche\Mercredi\Admin\Repository\SanteReponseRepository;
use AcMarche\Mercredi\Admin\Repository\TuteurRepository;
use AcMarche\Mercredi\Admin\Service\EnfantUtils;
use AcMarche\Mercredi\Admin\Service\FileUploader;
use AcMarche\Mercredi\Admin\Service\MailerService;
use AcMarche\Mercredi\Admin\Service\PresenceService;
use AcMarche\Mercredi\Admin\Service\TuteurUtils;
use AcMarche\Mercredi\Api\Service\Serializer;
use AcMarche\Mercredi\Api\Service\UpdateObject;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use AcMarche\Mercredi\Plaine\Service\PlaineService;
use AcMarche\Mercredi\Security\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class DefaultController
 * @package AcMarche\Mercredi\Api\Controller
 * @Route("/api")
 */
class DefaultController extends AbstractController
{
    /**
     * @var EcoleRepository
     */
    private $ecoleRepository;
    /**
     * @var JourRepository
     */
    private $jourRepository;
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;
    /**
     * @var EnfantUtils
     */
    private $enfantUtils;
    /**
     * @var PlaineService
     */
    private $plaineService;
    /**
     * @var PresenceService
     */
    private $presenceService;
    /**
     * @var PaiementRepository
     */
    private $paiementRepository;
    /**
     * @var EnfantTuteurRepository
     */
    private $enfantTuteurRepository;
    /**
     * @var Serializer
     */
    private $serializer;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    /**
     * @var MailerService
     */
    private $mailerService;
    /**
     * @var UpdateObject
     */
    private $updateObject;
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var FileUploader
     */
    private $fileUploader;
    /**
     * @var SanteQuestionRepository
     */
    private $santeQuestionRepository;
    /**
     * @var SanteFicheRepository
     */
    private $santeFicheRepository;
    /**
     * @var SanteReponseRepository
     */
    private $santeReponseRepository;


    public function __construct(
        EcoleRepository $ecoleRepository,
        JourRepository $jourRepository,
        TuteurRepository $tuteurRepository,
        EnfantUtils $enfantUtils,
        PlaineService $plaineService,
        PresenceService $presenceService,
        PaiementRepository $paiementRepository,
        EnfantTuteurRepository $enfantTuteurRepository,
        UserRepository $userRepository,
        Serializer $serializer,
        UserPasswordEncoderInterface $userPasswordEncoder,
        MailerService $mailerService,
        UpdateObject $updateObject,
        EnfantRepository $enfantRepository,
        FileUploader $fileUploader,
        SanteQuestionRepository $santeQuestionRepository,
        SanteFicheRepository $santeFicheRepository,
        SanteReponseRepository $santeReponseRepository
    ) {
        $this->ecoleRepository = $ecoleRepository;
        $this->jourRepository = $jourRepository;
        $this->tuteurRepository = $tuteurRepository;
        $this->enfantUtils = $enfantUtils;
        $this->plaineService = $plaineService;
        $this->presenceService = $presenceService;
        $this->paiementRepository = $paiementRepository;
        $this->enfantTuteurRepository = $enfantTuteurRepository;
        $this->serializer = $serializer;
        $this->userRepository = $userRepository;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->mailerService = $mailerService;
        $this->updateObject = $updateObject;
        $this->enfantRepository = $enfantRepository;
        $this->fileUploader = $fileUploader;
        $this->santeQuestionRepository = $santeQuestionRepository;
        $this->santeFicheRepository = $santeFicheRepository;
        $this->santeReponseRepository = $santeReponseRepository;
    }

    /**
     *
     * @Route("/all", methods={"GET"})
     *
     */
    public function index(Request $request)
    {
        $user = $this->getUser();
        $tuteur = TuteurUtils::getTuteurByUser($user);
        if (!$tuteur) {
            return $this->sendError('Pas de tuteur associé'.$user->getUserName());
        }

        $ecoles = $this->serializer->serializeEcole($this->ecoleRepository->findAll());
        $annees = $this->serializer->serializeAnnee(ScolaireService::getAnneesScolaires());
        $questions = $this->serializer->serializeQuestion($this->santeQuestionRepository->findAll());

        //118 et 502
        $tuteurSerialize = $this->serializer->serializeTuteur($tuteur);

        //  $paiements = $this->paiementRepository->findBy(['tuteur' => $tuteur->id]);
        $enfants = [];

        $joursTous = [];
        $enfantTuteurs = $this->enfantTuteurRepository->findBy(['tuteur' => $tuteur->getId()]);
        foreach ($enfantTuteurs as $enfantTuteur) {
            $enfant = $enfantTuteur->getEnfant();
            $jours = $this->jourRepository->getForParent($enfant);
            $joursTous += $this->serializer->serializeJour($jours, $enfant);
            $enfants[] = $enfant;
        }

        $this->serializer->serializeJour($jours, $enfant);

        $presences = [];
        foreach ($enfants as $enfant) {
            $presences +=
                $this->presenceService->getPresences(
                    $enfant,
                    $tuteur
                );
        }

        $presences = $this->serializer->serializePresences($presences);
        $santeFiches = $this->santeFicheRepository->getByEnfants($enfants);
        $reponses = $this->serializer->serializeReponse($this->santeReponseRepository->getBySanteFiche($santeFiches));

        $santeFiches = $this->serializer->serializeSanteFiche($santeFiches);

        $enfants = $this->serializer->serializeEnfant($enfants);
        $plaine = $this->serializer->serializePlaine($this->plaineService->getPlaineOuverte());

        $data = [
            "enfants" => $enfants,
            "tuteur" => $tuteurSerialize,
            "ecoles" => $ecoles,
            "jours" => $joursTous,
            "annees" => $annees,
            "plaine" => $plaine,
            "presences" => $presences,
            "santeQuestions" => $questions,
            "santeReponses" => $reponses,
            "santeFiches" => $santeFiches,
        ];

        return new JsonResponse($data);
    }

    /**
     *
     * @Route("/update/tuteur", methods={"POST"})
     * @IsGranted("index_tuteur")
     */
    public function updateTuteur(Request $request)
    {
        $user = $this->getUser();
        $tuteur = TuteurUtils::getTuteurByUser($user);
        if (!$tuteur) {
            return $this->sendError('Pas de tuteur associé'.$user->getUserName());
        }

        try {

            $tuteurData = json_decode($request->getContent());

            if ($tuteur->getId() == $tuteurData->id) {

                $oldTuteur = clone $tuteur;
                $this->updateObject->updateTuteur($tuteur, $tuteurData);

                $this->mailerService->sendContactTuteurChange($tuteur, $oldTuteur, $user->getEmail());
            }

            $this->tuteurRepository->save();

        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage());
        }

        return new JsonResponse($tuteurData);
    }

    /**
     *
     * @Route("/update/enfant/{id}", methods={"POST"})
     * @IsGranted("edit", subject="enfant")
     */
    public function updateEnfant(Request $request, Enfant $enfant)
    {
        try {

            $enfantData = json_decode($request->getContent());

            $this->updateObject->updateEnfant($enfant, $enfantData);

            $this->enfantRepository->save();

        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage());
        }

        return new JsonResponse($enfantData);
    }

    /**
     *
     * @Route("/update/enfant/photo/{id}", methods={"POST"})
     * @IsGranted("edit", subject="enfant")
     */
    public function updateEnfantPhoto(Request $request, Enfant $enfant)
    {
        $fileUploaded = $request->files->get('image');

        if (!$fileUploaded instanceof UploadedFile) {
            return $this->sendError("Image non reçue");
        }

        if ($fileUploaded->getError()) {
            return $this->sendError("error image");
        }

        if ($fileUploaded instanceof UploadedFile) {

            $photoName = md5(uniqid('', true)).'.'.$fileUploaded->guessExtension();

            try {
                $this->fileUploader->uploadEnfant("photo", $enfant->getId(), $fileUploaded, $photoName);

                $enfant->setImageName($photoName);
                $this->enfantRepository->save();

            } catch (FileException $error) {
                return $this->sendError($error->getMessage());
            }
        }

        return new JsonResponse($enfant);
    }

    /**
     *
     * @Route("/presences/new/{id}", methods={"POST"})
     * @IsGranted("edit", subject="enfant")
     */
    public function presenceNew(Request $request, Enfant $enfant)
    {
        $user = $this->getUser();
        $joursId = json_decode($request->request->get('jours'), true);

        if (!$joursId || count($joursId) == 0) {
            return $this->sendError("aucun jour recu");
        }

        try {
            $this->updateObject->insertPresences($user, $enfant, $joursId);

            return $this->sendSuccess("oki");
        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage());
        }

    }

    private function sendSuccess(string $message)
    {
        new JsonResponse(
            [
                'isError' => false,
                'message' => $message,
            ]
        );
    }

    private function sendError(string $message)
    {
        new JsonResponse(
            [
                'isError' => true,
                'message' => $message,
            ]
        );
    }


}