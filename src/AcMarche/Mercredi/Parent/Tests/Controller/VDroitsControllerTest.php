<?php

namespace AcMarche\Mercredi\Parent\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class VDroitsControllerTest extends BaseUnit
{
    public function testSelectEnfant()
    {
        $enfant = $this->getEnfant(['prenom' => 'Timeo']);
        $this->parent->request('GET', '/parent/presences/select/jour/'.$enfant->getUuid());
        $this->assertEquals(403, $this->parent->getResponse()->getStatusCode());
    }

    public function testSelectJour()
    {
        $this->parent->request('GET', '/parent/presences/select/new/leruth_timeo/39985');
        $this->assertEquals(404, $this->parent->getResponse()->getStatusCode());
    }

    public function testJourInexistant()
    {
        $this->parent->request('GET', '/parent/presences/select/new/michel_marie/39985');
        $this->assertEquals(404, $this->parent->getResponse()->getStatusCode());
    }
}
