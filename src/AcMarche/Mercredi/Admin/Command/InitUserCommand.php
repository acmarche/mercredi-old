<?php

namespace AcMarche\Mercredi\Admin\Command;

use AcMarche\Mercredi\Security\Entity\Group;
use AcMarche\Mercredi\Security\Entity\User;
use AcMarche\Mercredi\Security\Repository\GroupRepository;
use AcMarche\Mercredi\Security\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitUserCommand extends Command
{
    /**
     * @var GroupRepository
     */
    private $groupRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(GroupRepository $groupRepository, UserRepository $userRepository)
    {
        parent::__construct();
        $this->groupRepository = $groupRepository;
        $this->userRepository = $userRepository;
    }

    protected function configure()
    {
        $this
            ->setName('mercredi:inituser')
            ->setDescription('Encode les groupes et l\'utilisateur admin')
            ->addArgument('test', InputArgument::OPTIONAL, 'For phpunit');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $doctrine = $container->get('doctrine');

        $em = $doctrine->getManager();

        $this->createDefaultAccount($em, $output);
    }

    protected function createDefaultAccount(
        ObjectManager $entityManager,
        OutputInterface $output
    ) {
        $groupAdmin = $this->groupRepository->findOneBy(['name' => 'MERCREDI_ADMIN']);
        $groupRead = $this->groupRepository->findOneBy(['name' => 'ROLE_MERCREDI_READ']);
        $groupParent = $this->groupRepository->findOneBy(['name' => 'MERCREDI_PARENT']);
        $groupEcole = $this->groupRepository->findOneBy(['name' => 'MERCREDI_ECOLE']);
        $groupAnimateur = $this->groupRepository->findOneBy(['name' => 'MERCREDI_ANIMATEUR']);

        $adminUser = $this->userRepository->findOneBy(['username' => 'admin']);
        $readUser = $this->userRepository->findOneBy(['username' => 'read']);
        $ecoleUser = $this->userRepository->findOneBy(['username' => 'ecole']);
        $animateurUser = $this->userRepository->findOneBy(['username' => 'animateur']);
        $parentUser = $this->userRepository->findOneBy(['username' => 'parent']);

        if (!$groupAdmin) {
            $groupAdmin = new Group('MERCREDI_ADMIN');
            $groupAdmin->addRole('ROLE_MERCREDI_ADMIN');
            $groupAdmin->addRole('ROLE_MERCREDI_READ');
            $entityManager->persist($groupAdmin);
            $entityManager->flush();
            $output->writeln('Groupe MERCREDI_ADMIN créé');
        }

        if (!$groupParent) {
            $groupParent = new Group('MERCREDI_PARENT');
            $groupParent->addRole('ROLE_MERCREDI_PARENT');
            $entityManager->persist($groupParent);
            $this->addReference('group-parent', $groupParent);
            $entityManager->flush();
            $output->writeln('Groupe MERCREDI_PARENT créé');
        }

        if (!$groupAnimateur) {
            $groupAnimateur = new Group('MERCREDI_ANIMATEUR');
            $groupAnimateur->addRole('ROLE_MERCREDI_ANIMATEUR');
            $entityManager->persist($groupAnimateur);
            $this->addReference('group-animateur', $groupAnimateur);
            $entityManager->flush();
            $output->writeln('Groupe MERCREDI_ANIMATEUR créé');
        }

        if (!$groupEcole) {
            $groupEcole = new Group('MERCREDI_ECOLE');
            $groupEcole->addRole('ROLE_MERCREDI_ECOLE');
            $entityManager->persist($groupEcole);
            $this->addReference('group-ecole', $groupEcole);
            $entityManager->flush();
            $output->writeln('Groupe MERCREDI_ECOLE créé');
        }

        if (!$groupRead) {
            $groupRead = new Group('MERCREDI_READ');
            $groupRead->addRole('ROLE_MERCREDI_READ');
            $entityManager->persist($groupRead);
            $this->addReference('group-read', $groupRead);
            $entityManager->flush();
            $output->writeln('Groupe MERCREDI_READ créé');
        }

        if (!$adminUser) {
            $admin = new User();
            $admin->setUsername('admin');
            $admin->setNom('Admin');
            $admin->setPrenom('Zeze');
            $admin->setPlainPassword('admin');
            $admin->setEnabled(1);
            $admin->setEmail('jf@marche.be');
            $admin->addGroup($groupAdmin);
            $entityManager->persist($admin);
            $this->addReference('admin-user', $admin);
            $output->writeln("L'utilisateur admin a bien été créé");
        }

        if (!$parentUser) {
            $pmi = new User();
            $pmi->setUsername('pmichel');
            $pmi->setNom('Mich');
            $pmi->setPrenom('Phili');
            $pmi->setPlainPassword('admin');
            $pmi->setEnabled(1);
            $pmi->setEmail('webmaster@marche.be');
            $pmi->addGroup($groupParent);
            $entityManager->persist($pmi);
            $this->addReference('pmi', $pmi);
            $output->writeln("L'utilisateur pmi a bien été créé");
        }

        if (!$animateurUser) {
            $animateur = new User();
            $animateur->setUsername('animateur');
            $animateur->setNom('Vermoesen');
            $animateur->setPrenom('John');
            $animateur->setPlainPassword('animateur');
            $animateur->setEnabled(1);
            $animateur->setEmail('animateur@marche.be');
            $animateur->addGroup($groupAnimateur);
            $entityManager->persist($animateur);
            $output->writeln("L'utilisateur animateur a bien été créé");
        }

        if (!$ecoleUser) {
            $ecole = new User();
            $ecole->setUsername('ecole');
            $ecole->setNom('Ecole');
            $ecole->setPrenom('Aye');
            $ecole->setPlainPassword('ecole');
            $ecole->setEnabled(1);
            $ecole->setEmail('ecole@marche.be');
            $ecole->addGroup($groupEcole);
            $entityManager->persist($ecole);
            $output->writeln("L'utilisateur ecole a bien été créé");
        }

        if (!$readUser) {
            $read = new User();
            $read->setUsername('read');
            $read->setNom('Lecteur');
            $read->setPrenom('Ipod');
            $read->setPlainPassword('read');
            $read->setEnabled(1);
            $read->setEmail('read@marche.be');
            $read->addGroup($groupRead);
            $entityManager->persist($read);
        }

        if (!$ecoleUser->hasGroup($groupEcole)) {
            $ecoleUser->addGroup($groupEcole);
            $entityManager->persist($groupEcole);
            $output->writeln("L'utilisateur porte a été ajouté dans le groupe commerce");
        }

        if (!$animateurUser->hasGroup($groupAnimateur)) {
            $animateurUser->addGroup($groupAnimateur);
            $entityManager->persist($animateurUser);
            $output->writeln("L'utilisateur animateur a été ajouté dans le groupe animateur");
        }

        if (!$parentUser->hasGroup($groupParent)) {
            $parentUser->addGroup($groupParent);
            $entityManager->persist($parentUser);
            $output->writeln("L'utilisateur parent a été ajouté dans le groupe parent");
        }

        if (!$adminUser->hasGroup($groupAdmin)) {
            $adminUser->addGroup($groupAdmin);
            $entityManager->persist($adminUser);
            $output->writeln("L'utilisateur admin a été ajouté dans le groupe admin");
        }

        $entityManager->flush();
    }
}
