<?php

namespace AcMarche\Mercredi\Admin\DataFixtures\ORM;

use AcMarche\Mercredi\Admin\Entity\Ecole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadEcolesData extends Fixture implements ORMFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $ecoles = [
            'Athénée',
            'Aye communal',
            'Aye libre',
            'Hargimont communal',
            'Hollogne communal',
            'Humain communal',
            'IND',
            'On communal',
            'On libre',
            'Marloie libre',
            'Marloie école spéciale',
            'Saint-Martin',
            'Waha communal',
            'Autres écoles',
        ];

        foreach ($ecoles as $nom) {
            $ecole = new Ecole();
            $ecole->setNom($nom);
            $manager->persist($ecole);
            $this->addReference($nom, $ecole);
        }

        $manager->flush();
    }
}
