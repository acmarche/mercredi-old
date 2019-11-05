<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

/**
 * Test index page
 * Test add gratuit 100%
 * Test edit 100 % > 23 %.
 */
class AddReductionTest extends BaseUnit
{
    private $nom = 'Gratuit';
    private $pourcentageBad = 100;
    private $pourcentage = 23;

    public function testIndex()
    {
        $crawler = $this->admin->request('GET', '/admin/reduction/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
    }

    public function testAdd()
    {
        $crawler = $this->admin->request('GET', '/admin/reduction/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form([
            'reduction[nom]' => $this->nom,
            'reduction[pourcentage]' => $this->pourcentageBad,
        ]);

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("'.$this->nom.'")')->count());
    }

    public function testEdit()
    {
        $crawler = $this->admin->request('GET', '/admin/reduction/gratuit');
        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form([
            'reduction[pourcentage]' => $this->pourcentage,
        ]);

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("'.$this->pourcentage.' %")')->count());
    }

    public function testDelete()
    {
        $crawler = $this->admin->request('GET', '/admin/reduction/gratuit');
        $crawler = $this->admin->click($crawler->selectLink('Supprimer')->link());

        $this->admin->submit($crawler->selectButton('Supprimer')->form());
        $crawler = $this->admin->followRedirect();

        $this->assertNotRegExp('/Gratuit/', $this->admin->getResponse()->getContent());
    }
}
