<?php

namespace AcMarche\Mercredi\Parent\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class ADefaultControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->parent->request('GET', '/parent/');
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Bienvenue")')->count());
    }

    public function testContactIndex()
    {
        $crawler = $this->parent->request('GET', '/contact');
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Coordination")')->count());
    }

    public function testModaliteIndex()
    {
        $crawler = $this->parent->request('GET', '/modalite');
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Modalités pratiques")')->count());
    }

    public function testPaiementIndex()
    {
        $crawler = $this->parent->request('GET', '/parent/tuteur/paiements');
        $this->assertGreaterThan(0, $crawler->filter('td:contains("50.00 €")')->count());
    }
}
