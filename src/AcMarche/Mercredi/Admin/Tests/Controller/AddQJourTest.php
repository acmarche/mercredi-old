<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test index page
 * Test add 05-10-2016
 * Test edit prix1 7 => 12
 */
class AddQJourTest extends BaseUnit
{
    private $date1 = "05/10/2015";
    private $date1Link = '05-10-2015';
    private $date1LinkJour = "05-10-2016 Mercredi";
    private $prix1 = "15";
    private $prix1rectifie = "12";
    private $prix2 = "3";
    private $prix3 = "2";

    /**
     *
     */
    public function testIndex()
    {
        $crawler = $this->admin->request('GET', '/admin/jour/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
    }

    /**
     *
     */
    public function testAdd()
    {
        $crawler = $this->admin->request('GET', '/admin/jour/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form(array(
            'jour[date_jour]' => $this->date1,
            'jour[prix1]' => $this->prix1,
            'jour[prix2]' => $this->prix2,
            'jour[prix3]' => $this->prix3,
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("' . $this->prix1 . '.00 €")')->count());
    }

    public function testAddForParent()
    {
        $crawler = $this->admin->request('GET', '/admin/jour/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
        $dateParent = new \DateTime();
        $dateParent->modify('+8 week');

        $form = $crawler->selectButton('Ajouter')->form(array(
            'jour[date_jour]' => $dateParent->format('d/m/Y'),
            'jour[prix1]' => $this->prix1,
            'jour[prix2]' => $this->prix2,
            'jour[prix3]' => $this->prix3,
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
    }

    public function testEdit()
    {
        $crawler = $this->admin->request('GET', '/admin/jour/');

        $this->assertGreaterThan(0, $crawler->filter('td:contains("' . $this->prix1 . '.00 €")')->count());

        $crawler = $this->admin->click($crawler->selectLink($this->date1Link)->link());
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("' . $this->date1Link . '")')->count());

        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(array(
            'jour[prix1]' => $this->prix1rectifie,
        ));

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("' . $this->prix1rectifie . '.00 €")')->count());
    }

    public function testSetAnimateur()
    {
        $crawler = $this->admin->request('GET', '/admin/jour/');
        $crawler = $this->admin->click($crawler->selectLink($this->date1LinkJour)->link());
        $crawler = $this->admin->click($crawler->selectLink('Attribuer des animateurs')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(array());

        $form['jour_animateurs[animateurs][0]']->tick();
        $form['jour_animateurs[animateurs][1]']->tick();

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Les animateurs ont bien")')->count());
    }
}
