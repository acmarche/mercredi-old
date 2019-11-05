<?php

namespace AcMarche\Mercredi\Plaine\DataFixtures\ORM;

use AcMarche\Mercredi\Admin\DataFixtures\ORM\LoadUtilisateur;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPlaines extends Fixture implements ORMFixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $plaine1 = new Plaine();
        $plaine1->setIntitule('Toussaint 2016');
        $plaine1->setPrix1(7);
        $plaine1->setPrix2(5);
        $plaine1->setPrix3(3);
        $plaine1->setSlugname('toussaint-2016');

        $this->setData($plaine1);
        $manager->persist($plaine1);
        $this->addReference('toussaint-2016', $plaine1);

        $manager->flush();
    }

    public function setData(Plaine $plaine)
    {
        $plaine->setUserAdd($this->getReference('admin-user'));
        $plaine->setCreated(new \DateTime());
        $plaine->setUpdated(new \DateTime());
    }

    public function getOrder()
    {
        return 8; // the order in which fixtures will be loaded
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
