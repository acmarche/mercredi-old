<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

/*
 * Test de la page index
 * Je vais sur l enfant timeo
 * J'ajoute une presence pour la date du 05-10-2016
 *
 */

class PresenceTest extends BaseUnit
{
    //ajout d'une presence sans tuteur
    private $urlOrphelin = 'orphelin_kevin';

    //j'ajoute la date de 2015 a timeo (un seul parent)
    private $urlTimeo = 'leruth_timeo';
    private $date2015 = '05-10-2015 Lundi';

    //j'ajoute a liste la presence 1-11 en cherchant natacha
    private $urlLisa = 'michel_lisa';
    private $tuteur = 'SION Natacha';
    private $dateMardi2016 = '01-11-2016 Mardi';
    private $dateMardi2016Check = '01-11-2016';
    //j'ajoute aussi celle ci pour natacha
    private $dateSamedi2016Jour = '05-11-2016 Samedi';
    private $dateSamedi2016Check = '05-11-2016';

    public function testIndex()
    {
        $crawler = $this->admin->request('GET', '/admin/presence/');

        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Listing des présences")')->count());
    }

    public function testAddSantParent()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/'.$this->urlOrphelin);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Ajouter une présence')->link());

        $crawler = $this->admin->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('div:contains("L\'enfant n\'a pas de parent !")')->count());
    }

    public function testAddPresence()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/'.$this->urlTimeo);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Ajouter une présence')->link());
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Nouvelle présence")')->count());
        $form = $crawler->selectButton('Ajouter')->form([]);

        $option = $crawler->filter('#presence_jours option:contains("'.$this->date2015.'")');
        $this->assertEquals(1, count($option));
        $value = $option->attr('value');
        $form['presence[jours]']->select($value);

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("'.$this->date2015.'")')->count());
    }

    public function testShowPresence()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/'.$this->urlTimeo);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
        $crawler = $this->admin->click($crawler->selectLink(''.$this->date2015.'')->link());

        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
    }

    public function testAddWith2Tuteurs()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/'.$this->urlLisa);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Ajouter une présence')->link());
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Nouvelle présence")')->count());
        $form = $crawler->selectButton('Ajouter')->form([]);

        $option2 = $crawler->filter('#presence_jours option:contains("'.$this->dateMardi2016.'")');
        $this->assertEquals(1, count($option2));
        $value2 = $option2->attr('value');
        $form['presence[jours]']->select($value2);

        $optionTuteur = $crawler->filter('#presence_tuteur option:contains("'.$this->tuteur.'")');
        $this->assertEquals(1, count($optionTuteur));
        $valueTuteur = $optionTuteur->attr('value');
        $form['presence[tuteur]']->select($valueTuteur);

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("'.$this->dateMardi2016Check.'")')->count());
    }

    public function testAddWith2()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/'.$this->urlLisa);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Ajouter une présence')->link());
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Nouvelle présence")')->count());
        $form = $crawler->selectButton('Ajouter')->form([]);

        $option2 = $crawler->filter('#presence_jours option:contains("'.$this->dateSamedi2016Jour.'")');
        $this->assertEquals(1, count($option2));
        $value2 = $option2->attr('value');
        $form['presence[jours]']->select($value2);

        $optionTuteur = $crawler->filter('#presence_tuteur option:contains("'.$this->tuteur.'")');
        $this->assertEquals(1, count($optionTuteur));
        $valueTuteur = $optionTuteur->attr('value');
        $form['presence[tuteur]']->select($valueTuteur);

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("'.$this->dateSamedi2016Check.'")')->count());
    }
}
