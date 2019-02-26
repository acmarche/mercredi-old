<?php

namespace AcMarche\Mercredi\Animateur\Controller;

use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Form\Search\SearchTuteurType;
use AcMarche\Mercredi\Admin\Repository\EnfantRepository;
use AcMarche\Mercredi\Admin\Repository\EnfantTuteurRepository;
use AcMarche\Mercredi\Admin\Repository\PresenceRepository;
use AcMarche\Mercredi\Admin\Repository\TuteurRepository;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tuteur")
 * @IsGranted("ROLE_MERCREDI_ANIMATEUR")
 *
 */
class TuteurController extends AbstractController
{
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var PlainePresenceRepository
     */
    private $plainePresenceRepository;
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;
    /**
     * @var EnfantTuteurRepository
     */
    private $enfantTuteurRepository;

    public function __construct(
        PresenceRepository $presenceRepository,
        PlainePresenceRepository $plainePresenceRepository,
        TuteurRepository $tuteurRepository,
        EnfantTuteurRepository $enfantTuteurRepository,
        SessionInterface $session
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->session = $session;
        $this->tuteurRepository = $tuteurRepository;
        $this->enfantTuteurRepository = $enfantTuteurRepository;
    }

    /**
     *
     * @Route("/", name="animateur_tuteur")
     * @Route("/all/{all}", name="animateur_tuteur_all")
     * @IsGranted("ROLE_MERCREDI_ANIMATEUR")
     */
    public function index(Request $request, $all = false)
    {
        $key = "tuteur_animateur_search";

        $data = array();
        $search = false;

        if ($this->session->has($key)) {
            $data = unserialize($this->session->get($key));
            $search = true;
        }

        $search_form = $this->createForm(
            SearchTuteurType::class,
            $data,
            array(
                'method' => 'GET',
            )
        );

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
            $search = true;
            if ($search_form->get('raz')->isClicked()) {
                $this->session->remove($key);
                $this->addFlash('success', 'La recherche a bien été réinitialisée.');

                return $this->redirectToRoute('animateur_tuteur');
            }
            $this->session->set($key, serialize($data));
        }

        $tuteurs = [];
        if ($search) {
            $tuteurs = $this->tuteurRepository->quickSearch($data);
        }
        if ($all) {
            $tuteurs = $this->tuteurRepository->quickSearch([]);
            $search = true;
        }

        return $this->render(
            'animateur/tuteur/index.html.twig',
            array(
                'form' => $search_form->createView(),
                'tuteurs' => $tuteurs,
                'search' => $search,
                'all' => $all,
            )
        );
    }

    /**
     * @Route("/{slugname}", name="animateur_tuteur_show", methods={"GET"})
     *
     */
    public function show(Tuteur $tuteur)
    {
        return $this->render(
            'animateur/tuteur/show.html.twig',
            array(
                'tuteur' => $tuteur,
            )
        );
    }

}
