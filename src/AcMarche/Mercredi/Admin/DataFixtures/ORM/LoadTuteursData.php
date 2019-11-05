<?php

namespace AcMarche\Mercredi\Admin\DataFixtures\ORM;

use AcMarche\Mercredi\Admin\Entity\Tuteur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTuteursData extends Fixture implements ORMFixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $philippe = new Tuteur();
        $philippe->setNom('Michel');
        $philippe->setPrenom('Philippe');
        $philippe->setGsm('0475 52 33');
        $philippe->setSlugname('michel_philippe');
        $philippe->setSexe('Masculin');
        $philippe->setUserAdd($this->getReference('admin-user'));
        $philippe->setUser($this->getReference('pmi'));
        $philippe->setCreated(new \DateTime());
        $philippe->setUpdated(new \DateTime());

        $manager->persist($philippe);
        $this->addReference('parent-philippe', $philippe);

        $sion = new Tuteur();
        $sion->setNom('Sion');
        $sion->setPrenom('Natacha');
        $sion->setSlugname('sion_natacha');
        $sion->setSexe('Féminin');
        $sion->setUserAdd($this->getReference('admin-user'));
        $sion->setCreated(new \DateTime());
        $sion->setUpdated(new \DateTime());

        $manager->persist($sion);
        $this->addReference('parent-sion', $sion);

        $adrian = new Tuteur();
        $adrian->setNom('Pennino');
        $adrian->setPrenom('Adrian');
        $adrian->setSlugname('pennino_adrian');
        $adrian->setSexe('Féminin');
        $adrian->setUserAdd($this->getReference('admin-user'));
        $adrian->setCreated(new \DateTime());
        $adrian->setUpdated(new \DateTime());

        $manager->persist($adrian);

        $enfantTuteur = new \AcMarche\Mercredi\Admin\Entity\EnfantTuteur();
        $enfantTuteur->setEnfant($this->getReference('enfant-marie'));
        $enfantTuteur->setTuteur($philippe);

        $manager->persist($enfantTuteur);

        $enfantTuteur = new \AcMarche\Mercredi\Admin\Entity\EnfantTuteur();
        $enfantTuteur->setEnfant($this->getReference('enfant-lisa'));
        $enfantTuteur->setTuteur($philippe);

        $manager->persist($enfantTuteur);

        $enfantTuteur = new \AcMarche\Mercredi\Admin\Entity\EnfantTuteur();
        $enfantTuteur->setEnfant($this->getReference('enfant-arwen'));
        $enfantTuteur->setTuteur($philippe);

        $manager->persist($enfantTuteur);

        $enfantTuteur = new \AcMarche\Mercredi\Admin\Entity\EnfantTuteur();
        $enfantTuteur->setEnfant($this->getReference('enfant-zora'));
        $enfantTuteur->setTuteur($philippe);

        $manager->persist($enfantTuteur);

        $enfantTuteur = new \AcMarche\Mercredi\Admin\Entity\EnfantTuteur();
        $enfantTuteur->setEnfant($this->getReference('enfant-lisa'));
        $enfantTuteur->setTuteur($sion);

        $manager->persist($enfantTuteur);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [LoadUtilisateur::class, LoadEnfantsData::class];
    }
}
