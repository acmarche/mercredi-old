<?php

namespace AcMarche\Mercredi\Front\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->anonyme->request('GET', '/');

        $this->assertEquals(200, $this->anonyme->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Bienvenue")')->count() > 0);
    }
}
