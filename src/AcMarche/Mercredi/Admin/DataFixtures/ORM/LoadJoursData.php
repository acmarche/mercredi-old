<?php

namespace AcMarche\Mercredi\Admin\DataFixtures\ORM;

use AcMarche\Mercredi\Admin\Entity\Jour;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadJoursData extends Fixture implements ORMFixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $jour1 = new Jour();
        $jour1->setDateJour(new \DateTime('2016-10-05'));
        $this->setJour($jour1);
        $manager->persist($jour1);
        $this->addReference('2016-10-05', $jour1);

        $jour1 = new Jour();
        $jour1->setDateJour(new \DateTime('2016-10-12'));
        $this->setJour($jour1);
        $manager->persist($jour1);
        $this->addReference('2016-10-12', $jour1);

        $jour1 = new Jour();
        $jour1->setDateJour(new \DateTime('2016-10-17'));
        $this->setJour($jour1);
        $manager->persist($jour1);
        $this->addReference('2016-10-17', $jour1);

        $jour1 = new Jour();
        $jour1->setDateJour(new \DateTime('2016-10-19'));
        $this->setJour($jour1);
        $manager->persist($jour1);
        $this->addReference('2016-10-19', $jour1);

        $jour1 = new Jour();
        $jour1->setDateJour(new \DateTime('2016-10-26'));
        $this->setJour($jour1);
        $manager->persist($jour1);
        $this->addReference('2016-10-26', $jour1);

        $jour1 = new Jour();
        $jour1->setDateJour(new \DateTime('2016-10-29'));
        $this->setJour($jour1);
        $manager->persist($jour1);
        $this->addReference('2016-10-29', $jour1);

        $jour1 = new Jour();
        $jour1->setDateJour(new \DateTime('2016-11-01'));
        $this->setJour($jour1);
        $manager->persist($jour1);
        $this->addReference('2016-11-01', $jour1);

        $jour1 = new Jour();
        $jour1->setDateJour(new \DateTime('2016-11-05'));
        $this->setJour($jour1);
        $manager->persist($jour1);
        $this->addReference('2016-11-05', $jour1);

        $jour1 = new Jour();
        $jour1->setDateJour(new \DateTime('2016-11-08'));
        $this->setJour($jour1);
        $manager->persist($jour1);
        $this->addReference('2016-11-08', $jour1);

        $jour1 = new Jour();
        $jour1->setDateJour(new \DateTime('2016-11-11'));
        $this->setJour($jour1);
        $manager->persist($jour1);
        $this->addReference('2016-11-11', $jour1);

        $jour1 = new Jour();
        $jour1->setDateJour(new \DateTime('2015-11-11'));
        $this->setJour($jour1);
        $manager->persist($jour1);

        $jour1 = new Jour();
        $jour1->setDateJour(new \DateTime('2018-06-22'));
        $this->setJour($jour1);
        $manager->persist($jour1);

        $today = new Jour();
        $today->setDateJour(new \DateTime());
        $this->setJour($today);
        $manager->persist($today);

        $uneSemaine = new Jour();
        $date = new \DateTime();
        $date->modify('+7 day');
        $uneSemaine->setDateJour($date);
        $this->setJour($uneSemaine);
        $manager->persist($uneSemaine);

        $manager->flush();
    }

    public function setJour(Jour $jour)
    {
        $jour->setPrix1(5);
        $jour->setPrix2(3);
        $jour->setPrix3(2);
        $jour->setUserAdd($this->getReference('admin-user'));
        $jour->setCreated(new \DateTime());
        $jour->setUpdated(new \DateTime());
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
