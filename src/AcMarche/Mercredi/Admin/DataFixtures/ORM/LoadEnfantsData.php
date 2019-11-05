<?php

namespace AcMarche\Mercredi\Admin\DataFixtures\ORM;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadEnfantsData extends Fixture implements ORMFixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $lisa = new Enfant();
        $lisa->setNom('Michel');
        $lisa->setPrenom('Lisa');
        $lisa->setSexe('Féminin');
        $lisa->setBirthday(new \DateTime('1995-03-15'));
        $lisa->setSlugname('michel_lisa');
        $lisa->setAnneeScolaire('1M');
        $lisa->setOrdre(2);
        $lisa->setUserAdd($this->getReference('admin-user'));
        $lisa->setCreated(new \DateTime());
        $lisa->setUpdated(new \DateTime());

        $manager->persist($lisa);
        $this->addReference('enfant-lisa', $lisa);

        $marie = new Enfant();
        $marie->setNom('Michel');
        $marie->setPrenom('Marie');
        $marie->setSexe('Féminin');
        $marie->setBirthday(new \DateTime('1989-12-23'));
        $marie->setSlugname('michel_marie');
        $marie->setAnneeScolaire('6P');
        $marie->setOrdre(1);
        $marie->setUserAdd($this->getReference('admin-user'));
        $marie->setCreated(new \DateTime());
        $marie->setUpdated(new \DateTime());

        $manager->persist($marie);
        $this->addReference('enfant-marie', $marie);

        $arwen = new Enfant();
        $arwen->setNom('Michel');
        $arwen->setPrenom('Arwen');
        $arwen->setSexe('Féminin');
        $arwen->setBirthday(new \DateTime('2010-12-23'));
        $arwen->setSlugname('michel_arwen');
        $arwen->setAnneeScolaire('1P');
        $arwen->setOrdre(3);
        $arwen->setUserAdd($this->getReference('admin-user'));
        $arwen->setCreated(new \DateTime());
        $arwen->setUpdated(new \DateTime());

        $manager->persist($arwen);
        $this->addReference('enfant-arwen', $arwen);

        $zora = new Enfant();
        $zora->setNom('Michel');
        $zora->setPrenom('Zora');
        $zora->setSexe('Féminin');
        $zora->setBirthday(new \DateTime('2013-12-23'));
        $zora->setSlugname('michel_zora');
        $zora->setAnneeScolaire('1P');
        $zora->setOrdre(3);
        $zora->setUserAdd($this->getReference('admin-user'));
        $zora->setCreated(new \DateTime());
        $zora->setUpdated(new \DateTime());

        $manager->persist($zora);
        $this->addReference('enfant-zora', $zora);

        $paulie = new Enfant();
        $paulie->setNom('Pennino');
        $paulie->setPrenom('Paulie');
        $paulie->setSexe('Féminin');
        $paulie->setBirthday(new \DateTime('1995-03-15'));
        $paulie->setSlugname('pennino_paulie');
        $paulie->setAnneeScolaire('1M');
        $paulie->setOrdre(1);
        $paulie->setUserAdd($this->getReference('admin-user'));
        $paulie->setCreated(new \DateTime());
        $paulie->setUpdated(new \DateTime());
        $manager->persist($paulie);

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
