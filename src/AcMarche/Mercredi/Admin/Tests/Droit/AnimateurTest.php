<?php

namespace AcMarche\Mercredi\Admin\Tests\Droit;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

/**
 * Class TuteurTest
 * @package AcMarche\Admin\Admin\Tests\Droit
 */
class AnimateurTest extends BaseUnit
{
    public function testIndexTuteur()
    {
        //print_r($this->client->getResponse()->getContent());
        $url = '/animateur/tuteur/';

        $this->executeUrl($url, $this->admin, 403);
        $this->executeUrl($url, $this->animateur, 200);
        $this->executeUrl($url, $this->parent, 403);
        $this->executeUrl($url, $this->ecole, 403);
        $this->executeUrl($url, $this->read, 403);
    }

    public function testShowTuteur()
    {
        $url = '/animateur/tuteur/michel_philippe';

        $this->executeUrl($url, $this->admin, 403);
        $this->executeUrl($url, $this->animateur, 200);
        $this->executeUrl($url, $this->parent, 403);
        $this->executeUrl($url, $this->ecole, 403);
        $this->executeUrl($url, $this->read, 403);
    }

}
