<?php

namespace AcMarche\Mercredi\Plaine\DataFixtures\ORM;

use AcMarche\Mercredi\Admin\DataFixtures\ORM\LoadUtilisateur;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use AcMarche\Mercredi\Plaine\Entity\PlaineEnfant;

class LoadPresences extends Fixture implements ORMFixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $plaineEnfant = new PlaineEnfant();
        $plaineEnfant->setEnfant($this->getReference('enfant-lisa'))
            ->setPlaine($this->getReference('toussaint-2016'))
            ->setTuteur($this->getReference('parent-philippe'));

        $manager->persist($plaineEnfant);
        $this->addReference('plaine-lisa-philippe', $plaineEnfant);

        $plaineEnfant = new PlaineEnfant();
        $plaineEnfant->setEnfant($this->getReference('enfant-marie'))
            ->setPlaine($this->getReference('toussaint-2016'))
            ->setTuteur($this->getReference('parent-philippe'));

        $manager->persist($plaineEnfant);
        $this->addReference('plaine-marie-philippe', $plaineEnfant);

        $dates = array("2020-11-09", "2020-11-10");

        foreach ($dates as $date) {
            $presence = new PlainePresence();
            $presence->setJour($this->getReference($date))
                ->setPlaineEnfant($this->getReference('plaine-lisa-philippe'))
                ->setTuteur($this->getReference('parent-philippe'))
                ->setPaiement($this->getReference('paiement-plaine-lisa'));

            $this->setData($presence);
            $manager->persist($presence);

            $presence = new PlainePresence();
            $presence->setJour($this->getReference($date))
                ->setPlaineEnfant($this->getReference('plaine-marie-philippe'))
                ->setTuteur($this->getReference('parent-philippe'))
                ->setPaiement($this->getReference('paiement-plaine-marie'));

            $this->setData($presence);
            $manager->persist($presence);
        }

        $dates = array("2020-11-11");

        foreach ($dates as $date) {
            $presence = new PlainePresence();
            $presence->setJour($this->getReference($date))
                ->setPlaineEnfant($this->getReference('plaine-lisa-philippe'))
                ->setTuteur($this->getReference('parent-philippe'));

            $this->setData($presence);
            $manager->persist($presence);

            $presence = new PlainePresence();
            $presence->setJour($this->getReference($date))
                ->setPlaineEnfant($this->getReference('plaine-marie-philippe'))
                ->setTuteur($this->getReference('parent-philippe'));

            $this->setData($presence);
            $manager->persist($presence);
        }

        $manager->flush();
    }

    public function setData(PlainePresence $plaine)
    {
        $plaine->setUserAdd($this->getReference('admin-user'));
        $plaine->setCreated(new \DateTime());
        $plaine->setUpdated(new \DateTime());
    }

    public function getDependencies()
    {
        return [LoadUtilisateur::class, LoadJoursData::class];
    }
}
