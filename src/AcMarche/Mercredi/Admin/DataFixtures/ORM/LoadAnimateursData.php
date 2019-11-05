<?php

namespace AcMarche\Mercredi\Admin\DataFixtures\ORM;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAnimateursData extends Fixture implements ORMFixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $jason = new Animateur();
        $jason->setNom('Vermois');
        $jason->setPrenom('Jason');
        $jason->setGsm('0475 52 33');
        $jason->setEmail('jason@marche.be');
        $jason->setSlugname('vermois_jason');
        $jason->setUserAdd($this->getReference('admin-user'));
        $jason->setCreated(new \DateTime());
        $jason->setUpdated(new \DateTime());

        $manager->persist($jason);

        $jason2 = new Animateur();
        $jason2->setNom('Ledoux');
        $jason2->setPrenom('Alex');
        $jason2->setGsm('0475 10 22');
        $jason2->setEmail('alex@marche.be');
        $jason2->setSlugname('ledoux_alex');
        $jason2->setUserAdd($this->getReference('admin-user'));
        $jason2->setCreated(new \DateTime());
        $jason2->setUpdated(new \DateTime());

        $manager->persist($jason2);

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return array
     */
    public function getDependencies()
    {
        return [LoadUtilisateur::class];
    }
}
