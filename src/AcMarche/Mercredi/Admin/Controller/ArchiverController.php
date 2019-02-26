<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Plaine\Entity\Plaine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Jour;

/**
 * Archiver controller.
 *
 * @Route("/archiver")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class ArchiverController extends AbstractController
{
    /**
     * Archive a enfant
     *
     * @Route("/enfant/{slugname}", name="enfant_archiver", methods={"GET","POST"})
     *
     */
    public function enfant(Request $request, Enfant $enfant)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createArchiveEnfant($enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $label = $enfant->getArchive() ? 'désarchivé' : 'archivé';

            if ($enfant->getArchive()) {
                $enfant->setArchive(false);
            } else {
                $enfant->setArchive(true);
            }

            $em->persist($enfant);
            $em->flush();

            $this->addFlash('success', "L'enfant a bien été $label");

            return $this->redirectToRoute('enfant_show', array('slugname' => $enfant->getSlugname()));
        }

        return $this->render('admin/archiver/enfant.html.twig', array(
            'entity' => $enfant,
            'form' => $form->createView(),
        ));
    }

    protected function createArchiveEnfant(Enfant $enfant)
    {
        $label = $enfant->getArchive() ? 'Désarchiver' : 'Archiver';

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('enfant_archiver', array('slugname' => $enfant->getSlugname())))
            ->setMethod('POST')
            ->add('submit', SubmitType::class, array('label' => $label, 'attr' => array('class' => 'btn-success')))
            ->getForm();
    }

    /**
     * Archive a jour
     *
     * @Route("/jour/{id}", name="jour_archiver", methods={"GET","POST"})
     *
     */
    public function jour(Request $request, Jour $jour)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createArchiveJour($jour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $label = $jour->getArchive() ? 'désarchivé' : 'archivé';

            if ($jour->getArchive()) {
                $jour->setArchive(false);
            } else {
                $jour->setArchive(true);
            }

            $em->persist($jour);
            $em->flush();

            $this->addFlash('success', "Le jour de garde a bien été $label");

            return $this->redirectToRoute('jour_show', array('id' => $jour->getId()));
        }

        return $this->render('admin/archiver/jour.html.twig', array(
            'entity' => $jour,
            'form' => $form->createView(),
        ));
    }

    protected function createArchiveJour(Jour $jour)
    {
        $label = $jour->getArchive() ? 'Désarchiver' : 'Archiver';

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('jour_archiver', array('id' => $jour->getId())))
            ->setMethod('POST')
            ->add('submit', SubmitType::class, array('label' => $label, 'attr' => array('class' => 'btn-success')))
            ->getForm();
    }

    /**
     * Archive a plaine
     *
     * @Route("/plaine/{slugname}", name="plaine_archiver", methods={"GET","POST"})
     *
     */
    public function plaine(Request $request, Plaine $plaine)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createArchivePlaine($plaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $label = $plaine->getArchive() ? 'désarchivé' : 'archivé';

            if ($plaine->getArchive()) {
                $plaine->setArchive(false);
            } else {
                $plaine->setArchive(true);
            }

            $em->persist($plaine);
            $em->flush();

            $this->addFlash('success', "La plaine a bien été $label");

            return $this->redirectToRoute('plaine_show', array('slugname' => $plaine->getSlugname()));
        }

        return $this->render('admin/archiver/plaine.html.twig', array(
            'entity' => $plaine,
            'form' => $form->createView(),
        ));
    }

    protected function createArchivePlaine(Plaine $plaine)
    {
        $label = $plaine->getArchive() ? 'Désarchiver' : 'Archiver';

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('plaine_archiver', array('slugname' => $plaine->getSlugname())))
            ->setMethod('POST')
            ->add('submit', SubmitType::class, array('label' => $label, 'attr' => array('class' => 'btn-success')))
            ->getForm();
    }
}
