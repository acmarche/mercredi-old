<?php

namespace AcMarche\Mercredi\Animateur\Tests;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class DefaultControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->animateur->request('GET', '/animateur/');
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Bienvenue")')->count());
    }
}
