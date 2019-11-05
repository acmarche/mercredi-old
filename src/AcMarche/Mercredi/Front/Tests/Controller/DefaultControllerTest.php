<?php

namespace AcMarche\Mercredi\Front\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class DefaultControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->anonyme->request('GET', '/');

        $this->assertEquals(200, $this->anonyme->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Bienvenue")')->count() > 0);
    }
}
