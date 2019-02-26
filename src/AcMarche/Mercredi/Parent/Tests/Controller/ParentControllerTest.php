<?php

namespace AcMarche\Mercredi\Parent\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class ParentControllerTest extends BaseUnit
{
    public function testCoordonnees()
    {
        $crawler = $this->parent->request('GET', '/parent/tuteur/coordonnees');

        $this->assertGreaterThan(0, $crawler->filter('td:contains("0475 52 33")')->count());
    }

    public function testEnfants()
    {
        $crawler = $this->parent->request('GET', '/parent/enfants/');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("MICHEL Marie")')->count());
    }
}
