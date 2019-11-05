<?php

namespace AcMarche\Mercredi\Parent\Tests\Plaine;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class DefaultControllerTest extends BaseUnit
{
    public function testOpen()
    {
        $crawler = $this->admin->request('GET', '/plaine/plaine/');

        $crawler = $this->admin->click($crawler->selectLink('Toussaint 2016')->link());
        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            [
                'plaine_edit[inscriptionOuverture]' => 1,
            ]
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Inscriptions ouvertes aux parents !")')->count());
    }

    public function testIndex()
    {
        $crawler = $this->parent->request('GET', '/parent/');

        $crawler = $this->parent->click($crawler->selectLink('Toussaint 2016')->link());
        $crawler = $this->parent->click($crawler->selectLink('MICHEL Zora')->link());

        $lundi = \DateTime::createFromFormat('Y-m-d', '2020-11-09');
        $mardi = \DateTime::createFromFormat('Y-m-d', '2020-11-10');
        $lundi = $this->getPlaineJour(['date_jour' => $lundi]);
        $mardi = $this->getPlaineJour(['date_jour' => $mardi]);

        $form = $crawler->selectButton('Inscrire')->form(
            [
                'plaine_presence[jours][0]' => $lundi->getId(),
                'plaine_presence[jours][1]' => $mardi->getId(),
            ]
        );

        $this->parent->submit($form);
        $crawler = $this->parent->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("6,00 €")')->count());
    }

    public function testPlaineMax()
    {
        $crawler = $this->admin->request('GET', '/plaine/plaine/');

        $crawler = $this->admin->click($crawler->selectLink('Toussaint 2016')->link());
        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());
        $form = $crawler->selectButton('Mettre à jour')->form(
            [
                'plaine_edit[max][0][maximum]' => 1,
                'plaine_edit[max][1][maximum]' => 1,
                'plaine_edit[max][2][maximum]' => 1,
            ]
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertEquals(2, $crawler->filter('li:contains("petits : 1")')->count());
        $this->assertEquals(2, $crawler->filter('li:contains("moyens : 1")')->count());
        $this->assertEquals(2, $crawler->filter('li:contains("grands : 1")')->count());
    }

    public function testArwen()
    {
        $crawler = $this->parent->request('GET', '/parent/');

        $crawler = $this->parent->click($crawler->selectLink('Toussaint 2016')->link());
        $crawler = $this->parent->click($crawler->selectLink('MICHEL Arwen')->link());
        $crawler = $this->parent->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('div:contains("La fiche santé de votre enfant doit être complétée")')->count()
        );

        $crawler = $this->parent->click($crawler->selectLink('Modifier')->link());
        $form = $crawler->selectButton('Enregistrer')->form(
            [
                'sante_fiche[medecinNom]' => 'Ledoux',
                'sante_fiche[medecinTelephone]' => '084 52 98 22',
                'sante_fiche[personneUrgence]' => 'Maman et papa',
                'sante_fiche[questions][0][reponse]' => 0,
                'sante_fiche[questions][1][reponse]' => 0,
                'sante_fiche[questions][2][reponse]' => 0,
                'sante_fiche[questions][3][reponse]' => 0,
                'sante_fiche[questions][4][reponse]' => 0,
                'sante_fiche[questions][5][reponse]' => 0,
                'sante_fiche[questions][6][reponse]' => 0,
                'sante_fiche[questions][7][reponse]' => 0,
                'sante_fiche[questions][8][reponse]' => 0,
            ]
        );

        $this->parent->submit($form);
        $crawler = $this->parent->followRedirect();

        $this->assertEquals(0, $crawler->filter('td:contains("Non répondu")')->count());

        $crawler = $this->parent->request('GET', '/parent/');

        $crawler = $this->parent->click($crawler->selectLink('Toussaint 2016')->link());
        $crawler = $this->parent->click($crawler->selectLink('MICHEL Arwen')->link());

        $lundi = \DateTime::createFromFormat('Y-m-d', '2020-11-09');
        $mardi = \DateTime::createFromFormat('Y-m-d', '2020-11-10');
        $lundi = $this->getPlaineJour(['date_jour' => $lundi]);
        $mardi = $this->getPlaineJour(['date_jour' => $mardi]);

        $form = $crawler->selectButton('Inscrire')->form(
            [
                'plaine_presence[jours][0]' => $lundi->getId(),
                'plaine_presence[jours][1]' => $mardi->getId(),
            ]
        );

        $crawler = $this->parent->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('li:contains("La journée du 09-11-2020 Lundi est déjà complète.")')->count()
        );
        $this->assertGreaterThan(
            0,
            $crawler->filter('li:contains("La journée du 10-11-2020 Mardi est déjà complète.")')->count()
        );
    }
}
