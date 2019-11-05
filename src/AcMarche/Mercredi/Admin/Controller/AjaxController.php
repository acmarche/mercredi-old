<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\EnfantTuteur;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AjaxController.
 *
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class AjaxController extends AbstractController
{
    /**
     * Pour remplir l'auto completion.
     *
     * @Route("/tuteurcomplete/{query}", name="tuteur_autocomplete", methods={"GET"})
     */
    public function autocompleteTuteur($query = null)
    {
        $response = new JsonResponse();

        if (!$query) {
            $response->setData(['results' => []]);

            return $response;
        }

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository(Tuteur::class)->findForAutoComplete($query);
        $tuteurs = [];

        $i = 0;
        foreach ($entities as $tuteur) {
            $tuteurs[$i]['id'] = $tuteur->getId();
            $tuteurs[$i]['value'] = $tuteur->getId();
            $tuteurs[$i]['nom'] = $tuteur->getNom();
            $tuteurs[$i]['prenom'] = $tuteur->getPrenom();
            $tuteurs[$i]['label'] = $tuteur->getNom().' '.$tuteur->getPrenom();
            $birthday = '';
            if ($tuteur->getBirthday()) {
                $birthday = $tuteur->getBirthday()->format('d-m-Y');
            }

            $tuteurs[$i]['birthday'] = $birthday;
            ++$i;
        }

        $response->setData($tuteurs);

        return $response;
    }

    /**
     * Associe un tuteur a un enfant.
     *
     * @Route("/setenfant", name="tuteur_enfant", methods={"POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function setEnfant(Request $request)
    {
        $request->isXmlHttpRequest(); // is it an Ajax request?
        $idenfant = $request->request->get('idenfant');
        $idtuteur = $request->request->get('idtuteur');

        $message = ['error' => false, 'message' => ''];

        $em = $this->getDoctrine()->getManager();

        $enfant = $em->getRepository(Enfant::class)->find($idenfant);

        if (!$enfant) {
            $message = ['error' => true, 'message' => 'Veuillez choisir un enfant'];
        }

        $tuteur = $em->getRepository(Tuteur::class)->find($idtuteur);

        if (!$tuteur) {
            $message = ['error' => true, 'message' => 'Le tuteur est introuvable'];
        }

        if (false == $message['error']) {
            $enfant_tuteur = new EnfantTuteur();
            $enfant_tuteur->setEnfant($enfant);
            $enfant_tuteur->setTuteur($tuteur);

            $em->persist($enfant_tuteur);
            $em->flush();
            $message['message'] = "L'enfant a bien été associé";

            //    return $this->redirect($this->generateUrl('tuteur_show', array('slugname' => $tuteur->getSlugname())));
        }

        return $this->render('admin/ajax/ajax.html.twig', ['message' => $message]);
    }

    /**
     * Pour remplir l'auto completion.
     *
     * @Route("/enfantcomplete/{query}", name="enfant_autocomplete", methods={"GET"})
     */
    public function autocompleteEnfant($query = null)
    {
        $response = new JsonResponse();

        if (!$query) {
            $response->setData(['results' => []]);

            return $response;
        }

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(Enfant::class)->findForAutoComplete($query);
        $enfants = [];

        $i = 0;
        foreach ($entities as $enfant) {
            $enfants[$i]['id'] = $enfant->getId();
            $enfants[$i]['value'] = $enfant->getId();
            $enfants[$i]['nom'] = $enfant->getNom();
            $enfants[$i]['prenom'] = $enfant->getPrenom();
            $enfants[$i]['slugname'] = $enfant->getSlugname();
            $enfants[$i]['label'] = $enfant->getNom().' '.$enfant->getPrenom();
            $birthday = '';
            if ($enfant->getBirthday()) {
                $birthday = $enfant->getBirthday()->format('d-m-Y');
            }

            $enfants[$i]['birthday'] = $birthday;
            ++$i;
        }

        $response->setData($enfants);

        return $response;
    }

    /**
     * Associe un enfant au tuteur.
     *
     * @Route("/settuteur", name="enfant_tuteur", methods={"POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function setTuteur(Request $request)
    {
        $request->isXmlHttpRequest(); // is it an Ajax request?
        $idenfant = $request->request->get('idenfant');
        $idtuteur = $request->request->get('idtuteur');

        $message = ['error' => false, 'message' => ''];

        $em = $this->getDoctrine()->getManager();

        $enfant = $em->getRepository(Enfant::class)->find($idenfant);

        if (!$enfant) {
            $message = ['error' => true, 'message' => 'Veuillez choisir un enfant'];
        }

        $tuteur = $em->getRepository(Tuteur::class)->find($idtuteur);

        if (!$tuteur) {
            $message = ['error' => true, 'message' => 'Le tuteur est introuvable'];
        }

        if (false == $message['error']) {
            $enfant_tuteur = new EnfantTuteur();
            $enfant_tuteur->setEnfant($enfant);
            $enfant_tuteur->setTuteur($tuteur);

            $em->persist($enfant_tuteur);

            $em->flush();
            $message['message'] = 'Le tuteur a bien été associé';

            //    return $this->redirect($this->generateUrl('tuteur_show', array('slugname' => $tuteur->getSlugname())));
        }

        return $this->render('admin/ajax/ajax.html.twig', ['message' => $message]);
    }
}
