<?php

namespace AcMarche\Mercredi\Parent\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class NonLoggeControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $this->anonyme->request('GET', '/parent/');
        $this->assertEquals(302, $this->anonyme->getResponse()->getStatusCode());
    }
}
