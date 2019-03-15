<?php

namespace AcMarche\Mercredi\Admin\DataFixtures\ORM;

use AcMarche\Mercredi\Security\Entity\Group;
use AcMarche\Mercredi\Security\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoadUtilisateur extends Fixture implements ORMFixtureInterface
{
    /**
     * @var PasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $group_admin = new Group("MERCREDI_ADMIN");
        $group_admin->addRole("ROLE_MERCREDI_ADMIN");
        $group_admin->addRole("ROLE_MERCREDI_READ");
        $manager->persist($group_admin);

        $group_parent = new Group("MERCREDI_PARENT");
        $group_parent->addRole("ROLE_MERCREDI_PARENT");
        $manager->persist($group_parent);
        $this->addReference('group-parent', $group_parent);

        $group_animateur = new Group("MERCREDI_ANIMATEUR");
        $group_animateur->addRole("ROLE_MERCREDI_ANIMATEUR");
        $manager->persist($group_animateur);
        $this->addReference('group-animateur', $group_animateur);

        $group_ecole = new Group("MERCREDI_ECOLE");
        $group_ecole->addRole("ROLE_MERCREDI_ECOLE");
        $manager->persist($group_ecole);
        $this->addReference('group-ecole', $group_ecole);

        $group_read = new Group("MERCREDI_READ");
        $group_read->addRole("ROLE_MERCREDI_READ");
        $manager->persist($group_read);
        $this->addReference('group-read', $group_read);

        $admin = new User();
        $admin->setEmail("jf@marche.be");
        $admin->setUsername('admin');
        $admin->setNom('Admin');
        $admin->setPrenom('Zeze');
        $this->setPassword($admin, 'admin');
        $admin->setEnabled(1);
        $admin->addGroup($group_admin);
        $manager->persist($admin);
        $this->addReference('admin-user', $admin);

        $pmi = new User();
        $pmi->setEmail("pmichel@marche.be");
        $pmi->setUsername('pmichel@marche.be');
        $pmi->setNom('Mich');
        $pmi->setPrenom('Phili');
        $this->setPassword($pmi, 'admin');
        $pmi->setEnabled(1);
        $pmi->addGroup($group_parent);
        $manager->persist($pmi);
        $this->addReference('pmi', $pmi);

        $animateur = new User();
        $animateur->setEmail("animateur@marche.be");
        $animateur->setUsername('animateur@marche.be');
        $animateur->setNom('Vermoesen');
        $animateur->setPrenom('John');
        $this->setPassword($animateur, 'animateur');
        $animateur->setEnabled(1);
        $animateur->addGroup($group_animateur);
        $manager->persist($animateur);

        $read = new User();
        $read->setEmail("read@marche.be");
        $read->setUsername('read@marche.be');
        $read->setNom('Lecteur');
        $read->setPrenom('Ipod');
        $this->setPassword($read, 'read');
        $read->setEnabled(1);
        $read->addGroup($group_read);
        $manager->persist($read);

        $ecole = new User();
        $ecole->setEmail("ecole@marche.be");
        $ecole->setUsername('ecole@marche.be');
        $ecole->setNom('Ecole');
        $ecole->setPrenom('Aye');
        $this->setPassword($ecole, 'ecole');
        $ecole->setEnabled(1);
        $ecole->addGroup($group_ecole);
        $manager->persist($ecole);

        $manager->flush();
    }

    protected function setPassword(User $user, $password)
    {
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
    }
}
