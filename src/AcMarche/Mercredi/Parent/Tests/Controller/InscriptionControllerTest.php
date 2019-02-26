<?php

namespace AcMarche\Mercredi\Parent\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class InscriptionControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->admin->request('GET', '/security/utilisateurs/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $groupParent = $this->getGroup(['name' => 'MERCREDI_PARENT']);

        $form = $crawler->selectButton("Ajouter")->form(
            array(
                'utilisateur[nom]' => 'Sans',
                'utilisateur[prenom]' => 'Enfants',
                'utilisateur[email]' => 'solo@marche.be',
            )
        );

        $form['utilisateur[groups][1]'] = $groupParent->getId();

        $this->admin->submit($form);

        $crawler = $this->admin->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('div:contains("L\'utilisateur a bien été ajouté")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("MERCREDI_PARENT")')->count());
    }

    public function testAssociateSansTuteur()
    {
        $crawler = $this->admin->request('GET', '/security/utilisateurs/');

        $this->assertGreaterThan(0, $crawler->filter('td:contains("solo@marche.be")')->count());
        $crawler = $this->admin->click($crawler->selectLink('solo@marche.be')->link());

        $crawler = $this->admin->click($crawler->selectLink('Associer un parent')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(array());

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("solo@marche.be")')->count());
    }

    public function testChangePassword()
    {
        $crawler = $this->admin->request('GET', '/security/utilisateurs/');

        $crawler = $this->admin->click($crawler->selectLink('solo@marche.be')->link());

        $crawler = $this->admin->click($crawler->selectLink('Changer le mot de passe')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            [
                'user_password[password]' => 'homer123',
            ]
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Le mot de passe a bien été modifié.")')->count());
    }

    /**
     * pas de compte tuteur lie
     *
     */
    public function testDroitParent()
    {
        $this->solo->request('GET', '/parent/');
        $this->assertEquals(302, $this->solo->getResponse()->getStatusCode());

        $this->solo->request('GET', '/parent/enfants/');
        $this->assertEquals(403, $this->solo->getResponse()->getStatusCode());

        $this->solo->request('GET', '/parent/tuteur/paiements');
        $this->assertEquals(403, $this->solo->getResponse()->getStatusCode());

        $this->solo->request('GET', '/parent/tuteur/coordonnees');
        $this->assertEquals(403, $this->solo->getResponse()->getStatusCode());
    }
}
