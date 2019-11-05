<?php

namespace AcMarche\Mercredi\Admin\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPresence extends Fixture implements ORMFixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /**
         * Le 2016-10-05 lisa marie arwen.
         */
        $presence = new \AcMarche\Mercredi\Admin\Entity\Presence();
        $presence->setJour($this->getReference('2016-10-05'));
        $presence->setEnfant($this->getReference('enfant-lisa'));
        $presence->setPaiement($this->getReference('paiement-abonnement-lisa'));
        $this->setPresence($presence);
        $manager->persist($presence);

        $presence = new \AcMarche\Mercredi\Admin\Entity\Presence();
        $presence->setJour($this->getReference('2016-10-05'));
        $presence->setEnfant($this->getReference('enfant-marie'));
        $this->setPresence($presence);
        $manager->persist($presence);

        $presence = new \AcMarche\Mercredi\Admin\Entity\Presence();
        $presence->setJour($this->getReference('2016-10-05'));
        $presence->setEnfant($this->getReference('enfant-arwen'));
        $this->setPresence($presence);
        $manager->persist($presence);

        /**
         * le 2016-10-17 arwen seul.
         */
        $presence = new \AcMarche\Mercredi\Admin\Entity\Presence();
        $presence->setJour($this->getReference('2016-10-17'));
        $presence->setEnfant($this->getReference('enfant-arwen'));
        $presence->setReduction($this->getReference('cpas'));
        $this->setPresence($presence);
        $manager->persist($presence);

        /**
         * le 2016-10-19 lisa et arwen.
         */
        $presence = new \AcMarche\Mercredi\Admin\Entity\Presence();
        $presence->setJour($this->getReference('2016-10-19'));
        $presence->setEnfant($this->getReference('enfant-lisa'));
        $presence->setReduction($this->getReference('15-minutes'));
        $this->setPresence($presence);
        $manager->persist($presence);

        $presence = new \AcMarche\Mercredi\Admin\Entity\Presence();
        $presence->setJour($this->getReference('2016-10-19'));
        $presence->setEnfant($this->getReference('enfant-arwen'));
        $this->setPresence($presence);
        $manager->persist($presence);

        /**
         * le 2016-10-26 Marie et arwen.
         */
        $presence = new \AcMarche\Mercredi\Admin\Entity\Presence();
        $presence->setJour($this->getReference('2016-10-26'));
        $presence->setEnfant($this->getReference('enfant-marie'));
        $presence->setPaiement($this->getReference('paiement-abonnement-marie'));
        $this->setPresence($presence);
        $manager->persist($presence);

        $presence = new \AcMarche\Mercredi\Admin\Entity\Presence();
        $presence->setJour($this->getReference('2016-10-26'));
        $presence->setEnfant($this->getReference('enfant-arwen'));
        $this->setPresence($presence);
        $manager->persist($presence);

        /**
         * le 2016-10-29 Lisa et Marie absente.
         */
        $presence = new \AcMarche\Mercredi\Admin\Entity\Presence();
        $presence->setJour($this->getReference('2016-10-29'));
        $presence->setAbsent(true);
        $presence->setEnfant($this->getReference('enfant-marie'));
        $presence->setReduction($this->getReference('demi'));
        $this->setPresence($presence);
        $manager->persist($presence);

        $presence = new \AcMarche\Mercredi\Admin\Entity\Presence();
        $presence->setJour($this->getReference('2016-10-29'));
        $presence->setEnfant($this->getReference('enfant-lisa'));
        $this->setPresence($presence);
        $manager->persist($presence);

        /**
         * le 2016-10-12 zora avec lisa.
         */
        $presence = new \AcMarche\Mercredi\Admin\Entity\Presence();
        $presence->setJour($this->getReference('2016-10-12'));
        $presence->setEnfant($this->getReference('enfant-lisa'));
        $this->setPresence($presence);
        $manager->persist($presence);

        $presence = new \AcMarche\Mercredi\Admin\Entity\Presence();
        $presence->setJour($this->getReference('2016-10-12'));
        $presence->setEnfant($this->getReference('enfant-zora'));
        $this->setPresence($presence);
        $manager->persist($presence);

        /**
         * le 2016-11-01 zora seul.
         */
        $presence = new \AcMarche\Mercredi\Admin\Entity\Presence();
        $presence->setJour($this->getReference('2016-11-01'));
        $presence->setEnfant($this->getReference('enfant-zora'));
        $this->setPresence($presence);
        $manager->persist($presence);

        /**
         * le 2016-11-05 zora avec arwen.
         */
        $presence = new \AcMarche\Mercredi\Admin\Entity\Presence();
        $presence->setJour($this->getReference('2016-11-05'));
        $presence->setEnfant($this->getReference('enfant-zora'));
        $this->setPresence($presence);
        $manager->persist($presence);

        $presence = new \AcMarche\Mercredi\Admin\Entity\Presence();
        $presence->setJour($this->getReference('2016-11-05'));
        $presence->setEnfant($this->getReference('enfant-arwen'));
        $this->setPresence($presence);
        $manager->persist($presence);

        /**
         * 2016-10-08 lisa et marie.
         */
        $presence = new \AcMarche\Mercredi\Admin\Entity\Presence();
        $presence->setJour($this->getReference('2016-11-08'));
        $presence->setEnfant($this->getReference('enfant-lisa'));
        $this->setPresence($presence);
        $manager->persist($presence);

        $presence = new \AcMarche\Mercredi\Admin\Entity\Presence();
        $presence->setJour($this->getReference('2016-11-08'));
        $presence->setEnfant($this->getReference('enfant-marie'));
        $presence->setPaiement($this->getReference('paiement-abonnement-marie'));
        $this->setPresence($presence);
        $manager->persist($presence);

        /**
         * 2016-11-11 lisa seule.
         */
        $presence = new \AcMarche\Mercredi\Admin\Entity\Presence();
        $presence->setJour($this->getReference('2016-11-11'));
        $presence->setEnfant($this->getReference('enfant-lisa'));
        $presence->setPaiement($this->getReference('paiement-abonnement-lisa'));
        $this->setPresence($presence);
        $manager->persist($presence);

        $manager->flush();
    }

    public function setPresence($presence)
    {
        $presence->setTuteur($this->getReference('parent-philippe'));
        $presence->setUserAdd($this->getReference('admin-user'));
    }

    public function getDependencies()
    {
        return [
            LoadUtilisateur::class,
            LoadTuteursData::class,
            LoadJoursData::class,
            LoadPaiementData::class,
        ];
    }
}
