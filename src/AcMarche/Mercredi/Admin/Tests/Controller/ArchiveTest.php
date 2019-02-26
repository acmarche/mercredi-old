<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class ArchiveTest extends BaseUnit
{
    /**
     * Balboa est
     *  Ajoute
     *  Archive
     *  Desarchive
     */
    private $nom = "Balboa";
    private $prenom = "Rocky";
    private $sexe = "Masculin";
    private $ecoleHargi = "Hargimont communal";
    private $anneescolaire = "3M";

    /**
     * Le jour est
     *  Ajoute
     *  Archive
     *  Desarchive
     */
    private $date1 = "06/12/2015";
    private $date1LinkJour = "06-12-2015 Dimanche";
    private $prix1 = "15";
    private $prix2 = "3";
    private $prix3 = "2";

    public function testArchiveEnfant()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();

        $ecole = $this->getEcole($this->ecoleHargi);

        $form["enfant[nom]"] = $this->nom;
        $form["enfant[prenom]"] = $this->prenom;
        $form["enfant[sexe]"] = $this->sexe;
        $form['enfant[ecole]'] = $ecole->getId();
        $form["enfant[annee_scolaire]"] = $this->anneescolaire;

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("'.$this->prenom.'")')->count());

        $crawler = $this->admin->click($crawler->selectLink('Archiver')->link());

        $form = $crawler->selectButton('Archiver')->form(array());

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('div:contains("L\'enfant a bien été archivé")')->count());
    }

    public function testDesarchiveEnfant()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/'.$this->nom."_".$this->prenom);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Désarchiver')->link());

        $form = $crawler->selectButton('Désarchiver')->form(array());

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('div:contains("L\'enfant a bien été désarchivé")')->count());
    }

    public function testArchiveJour()
    {
        $crawler = $this->admin->request('GET', '/admin/jour/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form(
            array(
                'jour[date_jour]' => $this->date1,
                'jour[prix1]' => $this->prix1,
                'jour[prix2]' => $this->prix2,
                'jour[prix3]' => $this->prix3,

            )
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("'.$this->date1LinkJour.'")')->count());

        $crawler = $this->admin->click($crawler->selectLink($this->date1LinkJour)->link());

        $crawler = $this->admin->click($crawler->selectLink('Archiver')->link());

        $form = $crawler->selectButton('Archiver')->form(array());

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Le jour de garde a bien été archivé")')->count());
    }

    public function testDearchiveJour()
    {
        $crawler = $this->admin->request('GET', '/admin/archive/jours');
        $crawler = $this->admin->click($crawler->selectLink($this->date1LinkJour)->link());

        $crawler = $this->admin->click($crawler->selectLink('Désarchiver')->link());

        $form = $crawler->selectButton('Désarchiver')->form(array());

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('div:contains("Le jour de garde a bien été désarchivé")')->count()
        );
    }
}
