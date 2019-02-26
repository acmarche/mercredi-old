<?php

namespace AcMarche\Mercredi\Security\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class LoginControllerTest extends BaseUnit
{
    public function testParent()
    {
        $this->changePassword("rleffe@marche.be", "homer123");
        $crawler = $this->rleffe->request('GET', '/parent');

        $this->assertEquals(301, $this->rleffe->getResponse()->getStatusCode());

        $crawler = $this->rleffe->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('td:contains("LAMBERT John")')->count());
    }

    public function testEcoleSansEcole()
    {
        $this->changePassword("uharvard@marche.be", "homer123");
        $this->uharvard->request('GET', '/ecole/');
        $this->assertEquals(403, $this->uharvard->getResponse()->getStatusCode());
    }

    public function testEcole()
    {
        $crawler = $this->ecole->request('GET', '/ecole/');
        $this->assertEquals(200, $this->ecole->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('h3:contains("Liste des enfants")')->count() > 0);
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
