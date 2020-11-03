<?php

namespace AcMarche\Mercredi\Admin\DataFixtures\ORM;

use AcMarche\Mercredi\Admin\Entity\Reduction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadReductionData extends Fixture implements ORMFixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $reduction = new Reduction();
        $reduction->setNom('Cpas');
        $this->setData($reduction);
        $reduction->setPourcentage(100);
        $manager->persist($reduction);
        $this->addReference('cpas', $reduction);

        $reduction = new Reduction();
        $reduction->setNom('Moins de 15 minutes');
        $this->setData($reduction);
        $reduction->setPourcentage(100);
        $manager->persist($reduction);
        $this->addReference('15-minutes', $reduction);

        $reduction = new Reduction();
        $reduction->setNom('Demi journée');
        $this->setData($reduction);
        $reduction->setPourcentage(50);
        $manager->persist($reduction);
        $this->addReference('demi', $reduction);

        $manager->flush();
    }

    public function setData($reduction)
    {
        $reduction->setUserAdd($this->getReference('admin-user'));
        $reduction->setCreated(new \DateTime());
        $reduction->setUpdated(new \DateTime());
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
