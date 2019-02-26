<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

/**
 * Test page index animateur
 * Test add a animateur Collard dani
 * Test edit animateur renomme dani en dany
 */
class AddAnimateurTest extends BaseUnit
{
    private $nom = "Senechal";
    private $prenomBad = "Karine";
    private $prenom = "Carine";
    private $email = 'carine@marche.be';

    /**
     * Test page index
     */
    public function testIndex()
    {
        $crawler = $this->admin->request('GET', '/admin/animateur/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
    }

    /**
     * Test ajout
     *
     */
    public function testAddAnimateur()
    {
        $crawler = $this->admin->request('GET', '/admin/animateur/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form(array(
            'animateur[nom]' => $this->nom,
            'animateur[prenom]' => $this->prenomBad,
            'animateur[email]' => $this->email,
        ));

        $this->admin->submit($form);

        $crawler = $this->admin->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("' . strtoupper($this->nom) . '")')->count());
        $this->assertGreaterThan(0, $crawler->filter('div:contains("Un compte a été créé pour l\'animateur")')->count());
    }

    public function testEditAnimateur()
    {
        $crawler = $this->admin->request('GET', '/admin/animateur/' . $this->nom . '_' . $this->prenomBad . '');
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("' . strtoupper($this->nom) . '")')->count());

        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(array(
            'animateur_edit[prenom]' => $this->prenom,
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("' . $this->prenom . '")')->count());
    }

    public function testJoursGarde()
    {
        $crawler = $this->admin->request('GET', '/admin/animateur/' . $this->nom . '_' . $this->prenom . '');

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("' . strtoupper($this->nom) . '")')->count());

        $crawler = $this->admin->click($crawler->selectLink('Ses jours de présences')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(array());

        $form['animateur_jours[jours][0]']->tick();
        $form['animateur_jours[jours][1]']->tick();

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Les jours de présences ont bien été modifiés")')->count());
    }
}
