<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test page index tuteur
 * Test add a tuteur Collard dani
 * Test edit tuteur renomme dani en dany
 */
class AddParentTest extends BaseUnit
{
    private $nom = "Collard";
    private $prenomBad = "Dani";
    private $sexe = "Masculin";
    private $prenom = "Dany";

    /**
     * Test page index
     */
    public function testIndex()
    {
        $crawler = $this->admin->request('GET', '/admin/tuteur/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
    }

    /**
     * Test ajout
     *
     */
    public function testAddTuteur()
    {
        $crawler = $this->admin->request('GET', '/admin/tuteur/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form(array(
            'tuteur[nom]' => $this->nom,
            'tuteur[prenom]' => $this->prenomBad,
            'tuteur[sexe]' => $this->sexe,
            'tuteur[telephone]' => '084',
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("' . strtoupper($this->nom) . '")')->count());
    }

    public function testEditTuteur()
    {
        $crawler = $this->admin->request('GET', '/admin/tuteur/'.$this->nom."_".$this->prenomBad);
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("' . strtoupper($this->nom) . '")')->count());

        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(array(
            'tuteur[prenom]' => $this->prenom,
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("' . $this->prenom . '")')->count());
    }
}
