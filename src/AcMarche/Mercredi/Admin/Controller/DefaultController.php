<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package AcMarche\Admin\Admin\Controller
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home_admin")
     *
     */
    public function index()
    {
        return $this->render('admin/default/index.html.twig', array());
    }

    /**
     * @Route("/doc", name="doc")
     *
     */
    public function doc()
    {
        return $this->render('admin/default/doc.html.twig', []);
    }

    /**
     * @Route("/checkattach")
     *
     */
    public function checkAttachement()
    {
        $em = $this->getDoctrine()->getManager();
        $enfants = $em->getRepository(Enfant::class)->findAll();
        $lost = array();
        foreach ($enfants as $enfant) {
            $ficheName = $enfant->getFicheName();
            $fileName = $enfant->getFileName();

            if ($ficheName) {
                $enfant->setFileName($fileName);
            }

            if ($fileName) {
                $enfant->setFicheName($ficheName);
            }

            echo 'iiii';
        }

        return $this->render(
            'admin/default/check_attachement.html.twig',
            array(
                "losts" => $lost,
            )
        );
    }
}
