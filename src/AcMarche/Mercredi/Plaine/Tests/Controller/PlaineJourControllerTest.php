<?php

namespace AcMarche\Mercredi\Plaine\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PlaineJourControllerTest extends BaseUnit
{
    /**
     * J'ajoute la plaine et la date
     * puis je la supprime
     */
    private $nom = 'Adddates';
    private $dateAdd = "05/10/2020";
    private $dateAddTxt = "05-10-2020 Lundi";
    private $urlCarnaval = "carnaval_2020";
    private $dateToDel = "09-10-2020 Vendredi";
    private $dateAddCarva = "11/10/2020";
    private $dateAddCarvaTxt = "11-10-2020 Dimanche";

    /**
     * Test la page index
     * Test une page show
     */
    public function testPage()
    {
        // check index page
        $crawler = $this->admin->request('GET', '/plaine/plainejour/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
    }

    /**
     * Add date to paque
     */
    public function testAddDate()
    {
        // Create a new entry in the database
        $crawler = $this->admin->request('GET', '/plaine/plaine/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form(array(
            'plaine[intitule]' => $this->nom
        ));

        $this->admin->submit($form);

        $this->admin->followRedirect();

        $crawler = $this->admin->request('GET', '/plaine/plaine/' . $this->nom);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Ajouter une date')->link());

        $form = $crawler->selectButton('Ajouter')->form(array(
            'plaine_jour[date_jour]' => $this->dateAdd,
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('li:contains("' . $this->dateAddTxt . '")')->count());
    }

    public function testDeleteAdd()
    {
        $crawler = $this->admin->request('GET', '/plaine/plaine/' . $this->nom);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Supprimer')->link());

        $this->admin->submit($crawler->selectButton('Supprimer')->form());

        $this->admin->followRedirect();

        $this->assertNotRegExp('/' . $this->nom . '/', $this->admin->getResponse()->getContent());
    }

    /**
     * je retire une date puis la remet
     */
    public function testChangeDatePlaine()
    {
        $crawler = $this->admin->request('GET', '/plaine/plaine/' . $this->urlCarnaval);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink($this->dateToDel)->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Supprimer cette date de la plaine')->link());

        $this->admin->submit($crawler->selectButton('Supprimer')->form());

        $crawler = $this->admin->followRedirect();

        $this->assertNotRegExp('/' . $this->dateToDel . '/', $this->admin->getResponse()->getContent());

        $crawler = $this->admin->click($crawler->selectLink('Ajouter une date')->link());

        $form = $crawler->selectButton('Ajouter')->form(array(
            'plaine_jour[date_jour]' => $this->dateAddCarva,
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('li:contains("' . $this->dateAddCarvaTxt . '")')->count());
    }
}
