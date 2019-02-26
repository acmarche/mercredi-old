<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test joindre paiement a presence
 * Test suppression de ce paiement
 *
 * Test de la page principale
 */

class DefaultTest extends BaseUnit
{
    public function testSecuredHello()
    {
        $crawler = $this->admin->request('GET', '/admin/');

        $this->assertTrue($this->admin->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Accès rapide")')->count());
    }
}
