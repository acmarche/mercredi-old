<?php

namespace AcMarche\Mercredi\Admin\Tests\Droit;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

/**
 * Class IndexTest.
 */
class EnfantTest extends BaseUnit
{
    public function testIndexEnfant()
    {
        //print_r($this->client->getResponse()->getContent());
        $url = '/admin/enfant/';

        $this->executeUrl($url, $this->admin, 200);
        $this->executeUrl($url, $this->animateur, 403);
        $this->executeUrl($url, $this->parent, 403);
        $this->executeUrl($url, $this->ecole, 403);
        $this->executeUrl($url, $this->read, 200);
    }

    public function testShowEnfant()
    {
        $url = '/admin/enfant/michel_lisa';

        $this->executeUrl($url, $this->admin, 200);
        $this->executeUrl($url, $this->animateur, 403);
        $this->executeUrl($url, $this->parent, 403);
        $this->executeUrl($url, $this->ecole, 403);
        $this->executeUrl($url, $this->read, 200);
    }

    public function testEditEnfant()
    {
        $url = '/admin/enfant/michel_lisa/edit';

        $this->executeUrl($url, $this->admin, 200);
        $this->executeUrl($url, $this->animateur, 403);
        $this->executeUrl($url, $this->parent, 403);
        $this->executeUrl($url, $this->ecole, 403);
        $this->executeUrl($url, $this->read, 403);
    }

    public function testAddEnfant()
    {
        $url = '/admin/enfant/new';

        $this->executeUrl($url, $this->admin, 200);
        $this->executeUrl($url, $this->animateur, 403);
        $this->executeUrl($url, $this->parent, 403);
        $this->executeUrl($url, $this->ecole, 403);
        $this->executeUrl($url, $this->read, 403);
    }
}
