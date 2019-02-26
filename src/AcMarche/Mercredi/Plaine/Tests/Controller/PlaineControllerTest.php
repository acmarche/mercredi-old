<?php

namespace AcMarche\Mercredi\Plaine\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PlaineControllerTest extends BaseUnit
{
    private $notFound = "carnaval";
    private $nom = "Carnaval 2020";
    /**
     * test edit et del after
     */
    private $nomBad = "Poques";
    private $nomOk = "Pouque";
    private $nomToDel = "Titre nul";

    /**
     * Test la page index
     * Test plaine 404
     * Create carnaval
     * Test une page show
     * Add enfant sans date
     */
    public function testPage()
    {
        $this->admin->request('GET', '/plaine/plaine/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->admin->request('GET', '/plaine/plaine/' . $this->notFound);
        $this->assertTrue($this->admin->getResponse()->isNotFound());

        $crawler = $this->admin->request('GET', '/plaine/plaine/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form(array(
            'plaine[intitule]' => $this->nom
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("' . $this->nom . '")')->count());

        $this->admin->click($crawler->selectLink('Ajouter un enfant')->link());
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Cette plaine ne comporte aucune date !")')->count());
    }

    /**
     * Test page new plaine
     * Get page show
     * Test page edit
     */
    public function testForm()
    {
        $crawler = $this->admin->request('GET', '/plaine/plaine/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form(array(
            'plaine[intitule]' => $this->nomBad
        ));

        $this->admin->submit($form);
        $this->admin->followRedirect();

        $crawler = $this->admin->request('GET', '/plaine/plaine/poques');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Poques")')->count());

        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(array(
            'plaine_edit[intitule]' => $this->nomOk
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("' . $this->nomOk . '")')->count());
    }

    /**
     * je supprime paque
     * j'ajoute une plaine
     * puis la supprime
     */
    public function testDelete()
    {
        $crawler = $this->admin->request('GET', '/plaine/plaine/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form(array(
            'plaine[intitule]' => $this->nomToDel
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $crawler = $this->admin->click($crawler->selectLink('Supprimer')->link());

        $this->admin->submit($crawler->selectButton('Supprimer')->form());

        $this->admin->followRedirect();

        $this->assertNotRegExp('/' . $this->nomToDel . '/', $this->admin->getResponse()->getContent());

        $crawler = $this->admin->request('GET', '/plaine/plaine/' . $this->nomOk);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Supprimer')->link());

        $this->admin->submit($crawler->selectButton('Supprimer')->form());

        $crawler = $this->admin->followRedirect();

        $this->assertEquals(0, $crawler->filter('td:contains("' . $this->nomOk . '")')->count());
    }
}
