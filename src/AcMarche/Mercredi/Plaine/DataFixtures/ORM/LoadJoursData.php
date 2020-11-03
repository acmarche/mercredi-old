<?php

namespace AcMarche\Mercredi\Plaine\DataFixtures\ORM;

use AcMarche\Mercredi\Admin\DataFixtures\ORM\LoadUtilisateur;
use AcMarche\Mercredi\Plaine\Entity\PlaineJour;
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
        $dates = ['2020-11-09', '2020-11-10', '2020-11-11', '2020-11-12', '2020-11-13'];

        foreach ($dates as $date) {
            $jour1 = new PlaineJour();
            $jour1->setDateJour(new \DateTime($date));
            $this->setJour($jour1);
            $manager->persist($jour1);
            $this->addReference($date, $jour1);
        }

        $manager->flush();
    }

    public function setJour(PlaineJour $jour)
    {
        $jour->setPlaine($this->getReference('toussaint-2016'));
        $jour->setCreated(new \DateTime());
        $jour->setUpdated(new \DateTime());
    }

    public function getOrder()
    {
        return 9; // the order in which fixtures will be loaded
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return array
     */
    public function getDependencies()
    {
        return [LoadUtilisateur::class, LoadPlaines::class];
    }
}
