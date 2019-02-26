<?php

namespace AcMarche\Mercredi\Admin\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use AcMarche\Mercredi\Admin\Entity\Ecole;

class LoadEcolesData extends Fixture implements ORMFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $ecoles = array(
            "Athénée",
            "Aye communal",
            "Aye libre",
            "Hargimont communal",
            "Hollogne communal",
            "Humain communal",
            "IND",
            "On communal",
            "On libre",
            "Marloie libre",
            "Marloie école spéciale",
            "Saint-Martin",
            "Waha communal",
            "Autres écoles",
        );

        foreach ($ecoles as $nom) {
            $ecole = new Ecole();
            $ecole->setNom($nom);
            $manager->persist($ecole);
            $this->addReference($nom, $ecole);
        }

        $manager->flush();
    }
}
