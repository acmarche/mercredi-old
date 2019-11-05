<?php

namespace AcMarche\Mercredi\Admin\Tests\Droit;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

/**
 * Class NoteTest.
 */
class NoteTest extends BaseUnit
{
    private $note;

    public function testIndexNote()
    {
        //print_r($this->client->getResponse()->getContent());
        $url = '/admin/note/michel_lisa';

        $this->executeUrl($url, $this->admin, 200);
        $this->executeUrl($url, $this->animateur, 403);
        $this->executeUrl($url, $this->parent, 403);
        $this->executeUrl($url, $this->ecole, 403);
        $this->executeUrl($url, $this->read, 403);
    }

    public function testShowNote()
    {
        $url = '/admin/note/michel_lisa';

        $crawler = $this->executeUrl($url, $this->admin, 200);
        $crawler = $this->admin->click($crawler->selectLink('Coucou lisa')->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->executeUrl($url, $this->read, 403);
        $this->executeUrl($url, $this->animateur, 403);
        $this->executeUrl($url, $this->parent, 403);
        $this->executeUrl($url, $this->ecole, 403);
    }

    public function testEditNote()
    {
        $note = $this->getNote(['contenu' => 'Coucou lisa']);
        $url = '/admin/note/'.$note->getId().'/edit';

        $this->executeUrl($url, $this->admin, 200);
        $this->executeUrl($url, $this->animateur, 403);
        $this->executeUrl($url, $this->parent, 403);
        $this->executeUrl($url, $this->ecole, 403);
        $this->executeUrl($url, $this->read, 403);
    }

    public function testAddNote()
    {
        $url = '/admin/note/new/michel_lisa';

        $this->executeUrl($url, $this->admin, 200);
        $this->executeUrl($url, $this->animateur, 403);
        $this->executeUrl($url, $this->parent, 403);
        $this->executeUrl($url, $this->ecole, 403);
        $this->executeUrl($url, $this->read, 403);
    }
}
