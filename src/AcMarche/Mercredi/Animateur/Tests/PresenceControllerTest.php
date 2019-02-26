<?php

namespace AcMarche\Mercredi\Animateur\Tests;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class PresenceControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->animateur->request('GET', '/animateur/presence/');
        $this->assertEquals(200, $this->animateur->getResponse()->getStatusCode());

        $crawler = $this->animateur->click($crawler->selectLink('12-10-2016 Mercredi')->link());
        $this->assertEquals(200, $this->animateur->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('td:contains("Zora")')->count());
    }


}
