<?php

namespace AcMarche\Mercredi\Ecole\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class AssociateControllerTest extends BaseUnit
{
    public function testAssociate()
    {
        $crawler = $this->admin->request('GET', '/security/utilisateurs/');

        $this->assertGreaterThan(0, $crawler->filter('td:contains("ecole@marche.be")')->count());
        $crawler = $this->admin->click($crawler->selectLink('ecole@marche.be')->link());

        $crawler = $this->admin->click($crawler->selectLink('Associer une école')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            []
        );

        $ecole = $this->getEcole('Athénée');
        $form['associate_ecole[ecoles][0]'] = $ecole->getId();

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('td:contains("Athénée")')->count()
        );
    }
}
