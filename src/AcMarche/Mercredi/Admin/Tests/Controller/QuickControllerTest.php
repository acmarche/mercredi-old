<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

/**
 *
 */
class QuickControllerTest extends BaseUnit
{
    public function testAdd()
    {
        $crawler = $this->admin->request('GET', '/admin/quick/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();

        $ecole = $this->getEcole("Saint-Martin");

        $form["quick[tuteur][nom]"] = "Gonzales";
        $form["quick[tuteur][prenom]"] = "Speedy";
        $form["quick[tuteur][adresse]"] = "Rue de Francorchamps";
        $form["quick[tuteur][code_postal]"] = 6900;
        $form["quick[tuteur][localite]"] = "Champlon";
        $form["quick[tuteur][telephone]"] = "084 56 98 74";
        $form["quick[tuteur][email]"] = "speedy@marche.be";
        $form["quick[enfant][nom]"] = "Gonzales";
        $form["quick[enfant][prenom]"] = "BiipBeep";
        $form["quick[enfant][birthday][day]"] = 23;
        $form["quick[enfant][birthday][month]"] = 9;
        $form["quick[enfant][birthday][year]"] = 2014;
        $form["quick[enfant][annee_scolaire]"] = "1P";
        $form["quick[enfant][ecole]"] = $ecole->getId();

        $crawler = $this->admin->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Speedy")')->count());
        $this->assertGreaterThan(0, $crawler->filter('div:contains("BiipBeep")')->count());
        $this->assertGreaterThan(0, $crawler->filter('div:contains("speedy@marche.be")')->count());

        $crawler = $this->admin->request('GET', '/security/utilisateurs/');
        $crawler = $this->admin->click($crawler->selectLink('speedy@marche.be')->first()->link());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("MERCREDI_PARENT")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("GONZALES Speedy")')->count());
    }

    public function testAddSansCompte()
    {
        $crawler = $this->admin->request('GET', '/admin/quick/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();

        $ecole = $this->getEcole("Saint-Martin");

        $form["quick[tuteur][nom]"] = "Refractaire";
        $form["quick[tuteur][prenom]"] = "Lulu";
        $form["quick[tuteur][adresse]"] = "Rue de Francorchamps";
        $form["quick[tuteur][code_postal]"] = 6900;
        $form["quick[tuteur][localite]"] = "Champlon";
        $form["quick[tuteur][telephone]"] = "084 56 98 74";
        $form["quick[enfant][nom]"] = "Refractaire";
        $form["quick[enfant][prenom]"] = "Lolo";
        $form["quick[enfant][birthday][day]"] = 13;
        $form["quick[enfant][birthday][month]"] = 11;
        $form["quick[enfant][birthday][year]"] = 2015;
        $form["quick[enfant][annee_scolaire]"] = "1P";
        $form["quick[enfant][ecole]"] = $ecole->getId();

        $crawler = $this->admin->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Lulu")')->count());
        $this->assertGreaterThan(0, $crawler->filter('div:contains("Lolo")')->count());
    }

}
