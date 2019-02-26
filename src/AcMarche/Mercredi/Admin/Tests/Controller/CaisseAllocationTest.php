<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
 * Ajouter
 * Editer
 * Supprimer
 */

class CaisseAllocationTest extends BaseUnit
{
    private $nomBad = "Zeze";
    private $nom = "Zozo";

    public function testindex()
    {
        $crawler = $this->admin->request('GET', '/admin/caisseallocation/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
    }

    /**
     * Test page new
     * Test edit
     */
    public function testAdd()
    {
        $crawler = $this->admin->request('GET', '/admin/caisseallocation/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Ajouter')->form(array(
            'caisse_allocation[nom]' => $this->nomBad,
        ));

        $this->admin->submit($form);

        $crawler = $this->admin->followRedirect();
    }

    public function testEdit()
    {
        $crawler = $this->admin->request('GET', '/admin/caisseallocation/' . $this->nomBad);
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("' . $this->nomBad . '")')->count());

        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(array(
            'caisse_allocation[nom]' => $this->nom,
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("' . $this->nom . '")')->count());
    }

    public function TestDelete()
    {
        $crawler = $this->admin->request('GET', '/admin/caisseallocation/' . $this->nom);
        $crawler = $this->admin->click($crawler->selectLink('Supprimer')->link());

        $this->admin->submit($crawler->selectButton('Supprimer')->form());

        $crawler = $this->admin->followRedirect();

        $this->assertNotRegExp('/' . $this->nom . '/', $this->admin->getResponse()->getContent());
    }
}
