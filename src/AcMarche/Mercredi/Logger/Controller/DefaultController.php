<?php

namespace AcMarche\Mercredi\Logger\Controller;

use AcMarche\Mercredi\Logger\Entity\Log;
use AcMarche\Mercredi\Logger\Repository\LogRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package AcMarche\LoggerBundle\Controller
 * @Security("has_role('LOGGER_READ')")
 */
class DefaultController extends AbstractController
{
    /**
     * @var LogRepository
     */
    private $logRepository;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    /**
     * @Route("/", name="acmarche_logger_index")
     * @Template()
     */
    public function indexAction()
    {
        $logs = $this->logRepository->findAll();

        return ['logs' => $logs];
    }

    /**
     * @Route("/show/{id}", name="acmarche_logger_show")
     * @Template()
     */
    public function showAction(Log $log)
    {
        $values = array_values($log->getContext());

        return [
            'log' => $log,
            'values' => $values,
        ];
    }
}
