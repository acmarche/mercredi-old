<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class ZCheckup extends BaseUnit
{
    private $nom1 = 'LERUTH Timeo';
    private $nom2 = 'MICHEL Lisa';

    public function testPresenceNonPaye()
    {
        $crawler = $this->admin->request('GET', '/admin/checkup/presences/nonpayes');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('td:contains("'.$this->nom1.'")')->count());
    }

    public function testPaiement()
    {
        $crawler = $this->admin->request('GET', '/admin/checkup/paiement');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('td:contains("'.$this->nom2.'")')->count());
    }
}
