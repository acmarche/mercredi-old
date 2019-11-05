<?php

namespace AcMarche\Mercredi\Animateur\Tests;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class EnfantControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->animateur->request('GET', '/animateur/enfant/');

        $crawler = $this->animateur->click($crawler->selectLink('Afficher tous les enfants')->link());
        $crawler = $this->animateur->click($crawler->selectLink('Leruth')->link());
        $this->assertEquals(200, $this->animateur->getResponse()->getStatusCode());
        $crawler = $this->animateur->click($crawler->selectLink('Fiche santé')->link());
        $this->assertEquals(200, $this->animateur->getResponse()->getStatusCode());
    }
}
