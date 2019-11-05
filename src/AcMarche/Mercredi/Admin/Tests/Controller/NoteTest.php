<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class NoteTest extends BaseUnit
{
    //j'ajoute a liste la presence 1-11 en cherchant natacha
    private $urlLisa = 'michel_lisa';

    public function testAddNote()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/'.$this->urlLisa);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Ajouter une note')->link());

        $form = $crawler->selectButton('Ajouter')->form(
            ['note[contenu]' => 'Coucou lisa']
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Coucou lisa")')->count());
    }

    public function testShowNote()
    {
        $crawler = $this->admin->request('GET', '/admin/note/'.$this->urlLisa);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Coucou lisa')->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
    }
}
