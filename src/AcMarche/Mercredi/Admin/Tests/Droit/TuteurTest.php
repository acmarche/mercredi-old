<?php

namespace AcMarche\Mercredi\Admin\Tests\Droit;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

/**
 * Class TuteurTest
 * @package AcMarche\Admin\Admin\Tests\Droit
 */
class TuteurTest extends BaseUnit
{
    public function testIndexTuteur()
    {
        //print_r($this->client->getResponse()->getContent());
        $url = '/admin/tuteur/';

        $this->executeUrl($url, $this->admin, 200);
        $this->executeUrl($url, $this->animateur, 403);
        $this->executeUrl($url, $this->parent, 403);
        $this->executeUrl($url, $this->ecole, 403);
        $this->executeUrl($url, $this->read, 200);
    }

    public function testShowTuteur()
    {
        $url = '/admin/tuteur/michel_philippe';

        $this->executeUrl($url, $this->admin, 200);
        $this->executeUrl($url, $this->animateur, 403);
        $this->executeUrl($url, $this->parent, 403);
        $this->executeUrl($url, $this->ecole, 403);
        $this->executeUrl($url, $this->read, 200);
    }

    public function testEditTuteur()
    {
        $url = '/admin/tuteur/michel_philippe/edit';

        $this->executeUrl($url, $this->admin, 200);
        $this->executeUrl($url, $this->animateur, 403);
        $this->executeUrl($url, $this->parent, 403);
        $this->executeUrl($url, $this->ecole, 403);
        $this->executeUrl($url, $this->read, 403);
    }

    public function testAddTuteur()
    {
        $url = '/admin/tuteur/new';

        $this->executeUrl($url, $this->admin, 200);
        $this->executeUrl($url, $this->animateur, 403);
        $this->executeUrl($url, $this->parent, 403);
        $this->executeUrl($url, $this->ecole, 403);
        $this->executeUrl($url, $this->read, 403);
    }
}
