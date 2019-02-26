<?php

namespace AcMarche\Mercredi\Plaine\Tests\Z;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ZDeletePlaineTest extends BaseUnit
{
    public function testRemoveEnfant()
    {
        //je vais sur carnaval
        $crawler = $this->admin->request('GET', '/plaine/plaine/carnaval_2020');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET plaine/plaine/carnaval_2020");

        //je vais sur dineur
        $crawler = $this->admin->click($crawler->selectLink('LERUTH Timeo')->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET enfant LERUTH Timeo");

        $crawler = $this->admin->click($crawler->selectLink("Retirer l'enfant de la plaine")->link());
        $this->admin->submit($crawler->selectButton('Supprimer')->form());
        // print_r($this->admin->getResponse()->getContent());
        $crawler = $this->admin->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/LERUTH/', "Leruth Timeo tjs dans plaine");
    }

    public function testDeleteTimeo()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/leruth_timeo');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /admin/enfant/leruth_timeo");

        $crawler = $this->admin->click($crawler->selectLink("Supprimer l'enfant")->link());

        $this->admin->submit($crawler->selectButton('Supprimer')->form());

        $crawler = $this->admin->followRedirect();

        $this->assertNotRegExp('/Timeo/', 'Leruth timeo pas supprime');
    }

    /**
     * Je supprime les deux dates ajoutees
     * a carnaval
     */
    public function testDeleteDatesCarvanal()
    {
        $crawler = $this->admin->request('GET', '/plaine/plaine/carnaval_2020');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('10-10-2020 Samedi')->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Supprimer cette date de la plaine')->link());

        $this->admin->submit($crawler->selectButton('Supprimer')->form());

        $crawler = $this->admin->followRedirect();

        $this->assertNotRegExp('/10-10-2020 Samedi/', $this->admin->getResponse()->getContent());
    }

    public function testDeleteCarvanal()
    {
        $crawler = $this->admin->request('GET', '/plaine/plaine/carnaval_2020');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Supprimer la plaine')->link());

        $this->admin->submit($crawler->selectButton('Supprimer la plaine')->form());

        $crawler = $this->admin->followRedirect();

        $this->assertEquals(0, $crawler->filter('td:contains("Carnaval 2020")')->count());
    }
}
