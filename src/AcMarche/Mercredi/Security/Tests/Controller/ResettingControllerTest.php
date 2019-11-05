<?php

namespace AcMarche\Mercredi\Security\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class ResettingControllerTest extends BaseUnit
{
    public function testRequest()
    {
        $crawler = $this->anonyme->request('GET', '/reset/password/request');
        $this->assertEquals(200, $this->anonyme->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Demander un nouveau mot de passe')->form(
            [
                'lost_password[email_request]' => 'carine@marche.be',
            ]
        );

        $this->anonyme->submit($form);
        $crawler = $this->anonyme->followRedirect();

        $this->assertEquals(200, $this->anonyme->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('h3:contains("Confirmation")')->count() > 0);
    }

    public function testChange()
    {
        $token = $this->getUser(['email' => 'carine@marche.be'])->getConfirmationToken();
        $crawler = $this->anonyme->request('GET', '/reset/password/'.$token);
        $this->assertEquals(200, $this->anonyme->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Valider')->form(
            [
                'resetting_form[plainPassword][first]' => '123456789',
                'resetting_form[plainPassword][second]' => '123456789',
            ]
        );

        $this->anonyme->submit($form);

        $crawler = $this->anonyme->followRedirect();
        $this->assertEquals(200, $this->anonyme->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('div:contains("Votre mot de passe a bien été changé")')->count() > 0);
    }
}
