<?php

namespace AcMarche\Mercredi\Plaine\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->admin->request('GET', '/plaine');
        $this->assertEquals(301, $this->admin->getResponse()->getStatusCode());
        //   print_r($this->admin->getResponse()->getContent());
    }
}
