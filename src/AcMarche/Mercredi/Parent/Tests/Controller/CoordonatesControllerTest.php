<?php

namespace AcMarche\Mercredi\Parent\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class CoordonatesControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->parent->request('GET', '/parent/tuteur/coordonnees');
        $this->assertEquals(200, $this->parent->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('td:contains("0475 52 33")')->count());
    }

    public function testEdit()
    {
        $crawler = $this->parent->request('GET', '/parent/tuteur/coordonnees');
        $this->assertEquals(200, $this->parent->getResponse()->getStatusCode());

        $crawler = $this->parent->click($crawler->selectLink('Modifier mes coordonnées')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            [
                'coordonnees[email]' => 'pmi@marche.be',
            ]
        );

        $this->parent->submit($form);
        $crawler = $this->parent->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("pmi@marche.be")')->count());
    }
}
