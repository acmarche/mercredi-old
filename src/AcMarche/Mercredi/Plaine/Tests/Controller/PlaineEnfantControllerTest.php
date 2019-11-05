<?php

namespace AcMarche\Mercredi\Plaine\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class PlaineEnfantControllerTest extends BaseUnit
{
    private $urlCarnaval = 'carnaval_2020';
    /**
     * j'ajoute ces deux dates au carnaval.
     */
    private $dateAddCarnaval = '09/10/2020';
    private $dateAddCarnavalTxt = '09-10-2020 Vendredi';
    private $dateAddCarnaval2 = '10/10/2020';
    private $dateAddCarnavalTxt2 = '10-10-2020 Samedi';

    private $enfantAdd = 'LERUTH Timeo';
    private $enfantNom = 'LERUTH';

    /**
     * J'ajoute 2 dates a carnaval
     * J'ajoute un enfant.
     */
    public function testAddDates()
    {
        $crawler = $this->admin->request('GET', '/plaine/plaine/'.$this->urlCarnaval);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Ajouter une date')->link());

        $form = $crawler->selectButton('Ajouter')->form([
            'plaine_jour[date_jour]' => $this->dateAddCarnaval,
        ]);

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('li:contains("'.$this->dateAddCarnavalTxt.'")')->count());

        /**
         * en deux fois car multi field va pas cause jquery.
         */
        $crawler = $this->admin->click($crawler->selectLink('Ajouter une date')->link());

        $form = $crawler->selectButton('Ajouter')->form([
            'plaine_jour[date_jour]' => $this->dateAddCarnaval2,
        ]);

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('li:contains("'.$this->dateAddCarnavalTxt2.'")')->count());
    }

    public function testAddEnfant()
    {
        $crawler = $this->admin->request('GET', '/plaine/plaine/'.$this->urlCarnaval);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Ajouter un enfant')->link());

        $form = $crawler->selectButton('Ajouter')->form([]);

        //select 9-10-2020
        $form['plaine_presence[jours][0]']->tick();

        $option = $crawler->filter('#plaine_presence_enfant option:contains("'.$this->enfantAdd.'")');
        $this->assertEquals(1, count($option));
        $value = $option->attr('value');
        $form['plaine_presence[enfant]']->select($value);

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("'.$this->enfantNom.'")')->count());

        $this->admin->click($crawler->selectLink($this->enfantAdd)->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
    }
}
