<?php

namespace AcMarche\Mercredi\Animateur\Tests;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class ParentControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->animateur->request('GET', '/animateur/tuteur/');

        $crawler = $this->animateur->click($crawler->selectLink('Afficher tous les parents')->link());
        $crawler = $this->animateur->click($crawler->selectLink('MICHEL Philippe')->link());
        $this->assertEquals(200, $this->animateur->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('td:contains("0475 52 33")')->count());
    }


}
