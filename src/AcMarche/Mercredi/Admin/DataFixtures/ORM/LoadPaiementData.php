<?php

namespace AcMarche\Mercredi\Admin\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use AcMarche\Mercredi\Admin\Entity\Paiement;

class LoadPaiementData extends Fixture implements ORMFixtureInterface, DependentFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $paiement = new Paiement();
        $paiement->setMontant(22.50);
        $paiement->setDatePaiement(new \DateTime('2016-10-01'));
        $paiement->setEnfant($this->getReference('enfant-lisa'));
        $paiement->setTuteur($this->getReference('parent-philippe'));
        $paiement->setTypePaiement("Abonnement");
        $this->setJour($paiement);
        $manager->persist($paiement);
        $this->addReference('paiement-abonnement-lisa', $paiement);

        $paiement = new Paiement();
        $paiement->setMontant(25);
        $paiement->setDatePaiement(new \DateTime('2016-11-01'));
        $paiement->setEnfant($this->getReference('enfant-lisa'));
        $paiement->setTuteur($this->getReference('parent-philippe'));
        $paiement->setTypePaiement("Plaine");
        $this->setJour($paiement);
        $manager->persist($paiement);
        $this->addReference('paiement-plaine-lisa', $paiement);

        $paiement = new Paiement();
        $paiement->setMontant(40);
        $paiement->setDatePaiement(new \DateTime('2016-11-01'));
        $paiement->setEnfant($this->getReference('enfant-marie'));
        $paiement->setTuteur($this->getReference('parent-philippe'));
        $paiement->setTypePaiement("Abonnement");
        $this->setJour($paiement);
        $manager->persist($paiement);
        $this->addReference('paiement-abonnement-marie', $paiement);

        $paiement = new Paiement();
        $paiement->setMontant(50);
        $paiement->setDatePaiement(new \DateTime('2016-11-01'));
        $paiement->setEnfant($this->getReference('enfant-marie'));
        $paiement->setTuteur($this->getReference('parent-philippe'));
        $paiement->setTypePaiement("Plaine");
        $this->setJour($paiement);
        $manager->persist($paiement);
        $this->addReference('paiement-plaine-marie', $paiement);

        $manager->flush();
    }

    public function setJour($paiement)
    {
        $paiement->setUserAdd($this->getReference('admin-user'));
        $paiement->setCreated(new \DateTime());
        $paiement->setUpdated(new \DateTime());
    }

    public function getDependencies()
    {
        return [LoadUtilisateur::class, LoadEnfantsData::class, LoadTuteursData::class];
    }
}
