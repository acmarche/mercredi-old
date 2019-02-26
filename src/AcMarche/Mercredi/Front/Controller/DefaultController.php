<?php

namespace AcMarche\Mercredi\Front\Controller;

use AcMarche\Mercredi\Admin\Service\MailerService;
use AcMarche\Mercredi\Front\Form\ContactType;
use AcMarche\Mercredi\Security\Entity\User;
use AcMarche\Mercredi\Security\Manager\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
    /**
     * @var MailerService
     */
    private $mailerService;
    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(MailerService $mailerService, UserManager $userManager)
    {
        $this->mailerService = $mailerService;
        $this->userManager = $userManager;
    }

    /**
     *
     * @Route("/", name="homepage")
     *
     */
    public function index()
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user) {

            $roles = $this->userManager->getRolesForProfile($user);

              if (count($roles) > 1) {
                  return $this->redirectToRoute('mercredi_profile');
              }
              if ($user->hasRole('ROLE_MERCREDI_PARENT')) {
                  return $this->redirectToRoute('home_parent');
              }
              if ($user->hasRole('ROLE_MERCREDI_ECOLE')) {
                  return $this->redirectToRoute('home_ecole');
              }
              if ($user->hasRole('ROLE_MERCREDI_ANIMATEUR')) {
                  return $this->redirectToRoute('home_animateur');
              }
              if ($user->hasRole('ROLE_MERCREDI_ADMIN') or $user->hasRole('ROLE_MERCREDI_READ')) {
                  return $this->redirectToRoute('home_admin');
              }
        }

        return $this->render('front/default/index.html.twig');
    }

    /**
     *
     * @Route("/profile", name="mercredi_profile")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function selectProfile()
    {
        return $this->render('front/default/profile.html.twig');
    }

    /**
     * @Route("/contact", name="contact", methods={"GET","POST"})
     *
     */
    public function contact(Request $request)
    {
        $contactForm = $this->createForm(ContactType::class)
            ->add('submit', SubmitType::class, array('label' => 'Envoyer'));

        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $data = $contactForm->getData();
            $nom = $data['nom'];
            $email = $data['email'];
            $body = $data['texte'];

            $this->mailerService->sendContactForm($email, $nom, $body);

            $this->addFlash('success', 'Le message a bien été envoyé.');

            return $this->redirectToRoute('contact');
        }

        return $this->render(
            'front/default/contact.html.twig',
            array(
                'contact_form' => $contactForm->createView(),
            )
        );
    }

    /**
     *
     * @Route("/modalite", name="modalite")
     *
     */
    public function modalite()
    {
        return $this->render('front/default/modalite.html.twig', []);
    }

    /**
     * @Route("/blog", name="blog")
     *
     */
    public function blog()
    {
        return $this->render('front/default/blog.html.twig', []);
    }
}
