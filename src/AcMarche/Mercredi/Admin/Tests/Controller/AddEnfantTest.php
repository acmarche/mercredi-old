<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

/**
 * Je test page index enfant
 * J'ajoute un enfant Leruth Timeau
 * J'édite cet enfant et le renomme en Timeo
 * J'ajoute un enfant orphelin pour test presence sans parent
 */
class AddEnfantTest extends BaseUnit
{
    private $nom = "Leruth";
    private $prenomBad = "Timeau";
    private $sexe = "Masculin";
    private $ecoleHargi = "Hargimont communal";
    private $anneescolaire = "3M";
    private $prenom = "Timeo";

    private $nomOrphelin = "Orphelin";
    private $prenomOrphelin = "kevin";
    private $sexeOrphelin = "Masculin";
    private $ecoleOrphelin = "Hargimont communal";
    private $anneescolaireOrphelin = "3M";

    public function testIndex()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/');
        //print_r($this->admin->getResponse()->getContent());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
    }

    public function testAddEnfant()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();

        $ecole = $this->getEcole($this->ecoleHargi);

        $form["enfant[nom]"] = $this->nom;
        $form["enfant[prenom]"] = $this->prenomBad;
        $form["enfant[sexe]"] = $this->sexe;
        $form['enfant[ecole]'] = $ecole->getId();
        $form["enfant[annee_scolaire]"] = $this->anneescolaire;

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("'.$this->prenomBad.'")')->count());
    }

    public function testEditEnfant()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/'.$this->nom."_".$this->prenomBad);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->assertGreaterThan(
            0,
            $crawler->filter('h3:contains("'.strtoupper($this->nom).'")')->count(),
            'Missing element h3:contains("LERUTH")'
        );

        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            array(
                'enfant[prenom]' => $this->prenom,
            )
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('h3:contains("'.$this->prenom.'")')->count(),
            'Missing element h3:contains("Timeo")'
        );
    }

    public function testAddOrphelin()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $ecole = $this->getEcole($this->ecoleOrphelin);

        $form = $crawler->selectButton('Ajouter')->form(
            array(
                'enfant[nom]' => $this->nomOrphelin,
                'enfant[prenom]' => $this->prenomOrphelin,
                'enfant[sexe]' => $this->sexeOrphelin,
                'enfant[ecole]' => $ecole->getId(),
                'enfant[annee_scolaire]' => $this->anneescolaireOrphelin,
            )
        );

        $this->admin->submit($form);

        $crawler = $this->admin->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("'.strtoupper($this->nomOrphelin).'")')->count());
    }

    public function testPasFratrie()
    {
        $crawler = $this->admin->request('GET', "/admin/enfant/".$this->nomOrphelin."_".$this->prenomOrphelin);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
        //print_r($this->admin->getResponse()->getContent());
        $this->assertEquals(1, $crawler->filter('p:contains("Aucune")')->count());
    }
}
