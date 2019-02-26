<?php

namespace AcMarche\Mercredi\Animateur\Tests;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class FicheControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->animateur->request('GET', '/animateur/animateur/show');
        $this->assertEquals(302, $this->animateur->getResponse()->getStatusCode());
        $crawler = $this->animateur->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('div:contains("Aucune fiche relié à votre compte")')->count());
    }

    public function testRelie()
    {
        $crawler = $this->admin->request('GET', '/admin/animateur/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form(
            array(
                'animateur[nom]' => "Chirac",
                'animateur[prenom]' => "Patrick",
                'animateur[email]' => "animateur@marche.be",
            )
        );

        $this->admin->submit($form);

        $crawler = $this->admin->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("'.strtoupper("chirac").'")')->count());
        $this->assertGreaterThan(
            0,
            $crawler->filter('div:contains("Un compte a été créé pour l\'animateur")')->count()
        );
    }

    public function testFiche()
    {
        $this->changePassword("animateur@marche.be", "animateur");
        $crawler = $this->animateur->request('GET', '/animateur/');
        $this->assertEquals(200, $this->animateur->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Bienvenue Patrick")')->count());
        $crawler = $this->animateur->click($crawler->selectLink('Ma fiche')->link());

        $crawler = $this->animateur->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            array(
                'animateur_edit[adresse]' => "Rue du Lac",
            )
        );

        $this->animateur->submit($form);
        $crawler = $this->animateur->followRedirect();

        $this->assertTrue($crawler->filter('div:contains("Rue du Lac")')->count() > 0);
    }

    protected function changePassword(string $email, string $password)
    {
        $crawler = $this->admin->request('GET', '/security/utilisateurs/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink($email)->link());
        $crawler = $this->admin->click($crawler->selectLink('Changer le mot de passe')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            array(
                'user_password[password]' => $password,
            )
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();


        $this->assertTrue($crawler->filter('div:contains("Le mot de passe a bien été modifié.")')->count() > 0);
    }
}
