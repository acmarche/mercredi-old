<?php

namespace AcMarche\Mercredi\Ecole\Controller;

use AcMarche\Mercredi\Admin\Repository\EcoleRepository;
use AcMarche\Mercredi\Admin\Repository\EnfantRepository;
use AcMarche\Mercredi\Admin\Repository\JourRepository;
use AcMarche\Mercredi\Ecole\Form\SearchEnfantForEcoleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Ecole controller.
 *
 *
 * @IsGranted("ROLE_MERCREDI_ECOLE")
 */
class IndexController extends AbstractController
{
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var EcoleRepository
     */
    private $ecoleRepository;
    /**
     * @var JourRepository
     */
    private $jourRepository;

    public function __construct(
        EcoleRepository $ecoleRepository,
        EnfantRepository $enfantRepository,
        JourRepository $jourRepository
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->ecoleRepository = $ecoleRepository;
        $this->jourRepository = $jourRepository;
    }

    /**
     * Lists all Ecole entities.
     *
     * @Route("/", name="home_ecole", methods={"GET","POST"})
     * @IsGranted("index_ecole")
     */
    public function index(Request $request)
    {
        $enfants = [];

        $search_form = $this->createForm(SearchEnfantForEcoleType::class);

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
            $enfants = $this->enfantRepository->searchForEcole($data);
        }

        return $this->render(
            'ecole/index.html.twig',
            array(
                'form' => $search_form->createView(),
                'enfants' => $enfants,
            )
        );
    }
}
