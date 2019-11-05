<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class SanteControllerTest extends BaseUnit
{
    public function testEdit()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/michel_lisa');
        $crawler = $this->admin->click($crawler->selectLink('Fiche santé')->link());
        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Enregistrer')->form(
            [
                'sante_fiche[medecinNom]' => 'Ledoux',
                'sante_fiche[medecinTelephone]' => '084 52 98 22',
                'sante_fiche[personneUrgence]' => 'Maman et papa',
                'sante_fiche[questions][0][reponse]' => 0,
                'sante_fiche[questions][1][reponse]' => 1,
                'sante_fiche[questions][2][reponse]' => 1,
                'sante_fiche[questions][3][reponse]' => 0,
                'sante_fiche[questions][8][reponse]' => 0,
            ]
        );
        //print_r($this->admin->getResponse()->getContent());
        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertEquals(1, $crawler->filter('html:contains("Oui")')->count());
        $this->assertEquals(4, $crawler->filter('td:contains("Non répondu")')->count());
    }

    public function testComplment()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/michel_lisa');
        $crawler = $this->admin->click($crawler->selectLink('Fiche santé')->link());
        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Enregistrer')->form(
            [
                'sante_fiche[questions][8][reponse]' => 1,
            ]
        );

        $crawler = $this->admin->submit($form);

        $this->assertEquals(1, $crawler->filter('html:contains("Lesquels")')->count());

        $form = $crawler->selectButton('Enregistrer')->form(
            [
                'sante_fiche[questions][8][remarque]' => 'chocolat, pate',
                'sante_fiche[questions][0][remarque]' => 'Type 1',
            ]
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertEquals(1, $crawler->filter('td:contains("chocolat")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Type 1")')->count());
        $this->assertEquals(4, $crawler->filter('td:contains("Non répondu")')->count());
    }
}
