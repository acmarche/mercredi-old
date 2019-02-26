<?php

namespace AcMarche\Mercredi\Parent\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class SanteControllerTest extends BaseUnit
{
    public function testEdit()
    {
        $crawler = $this->parent->request('GET', '/parent/enfants/');
        $crawler = $this->parent->click($crawler->selectLink('MICHEL Marie')->link());
        $crawler = $this->parent->click($crawler->selectLink('Sa fiche santé')->link());
        $crawler = $this->parent->click($crawler->selectLink('Modifier')->link());

        $form = $crawler->selectButton('Enregistrer')->form(
            [
                'sante_fiche[medecinNom]' => "Ledoux",
                'sante_fiche[medecinTelephone]' => "084 52 98 22",
                'sante_fiche[personneUrgence]' => "Maman et papa",
                'sante_fiche[questions][0][reponse]' => 0,
                'sante_fiche[questions][1][reponse]' => 1,
                'sante_fiche[questions][2][reponse]' => 1,
            ]
        );

        $this->parent->submit($form);
        $crawler = $this->parent->followRedirect();

        $this->assertEquals(1, $crawler->filter('html:contains("Oui")')->count());
        $this->assertEquals(6, $crawler->filter('td:contains("Non répondu")')->count());
    }

    public function testComplment()
    {
        $crawler = $this->parent->request('GET', '/parent/enfants/');
        $crawler = $this->parent->click($crawler->selectLink('MICHEL Marie')->link());
        $crawler = $this->parent->click($crawler->selectLink('Sa fiche santé')->link());
        $crawler = $this->parent->click($crawler->selectLink('Modifier')->link());

        $form = $crawler->selectButton('Enregistrer')->form(
            [
                'sante_fiche[questions][8][reponse]' => 1,
            ]
        );

        $crawler = $this->parent->submit($form);

        $this->assertEquals(1, $crawler->filter('html:contains("Lesquels")')->count());

        $form = $crawler->selectButton('Enregistrer')->form(
            [
                'sante_fiche[questions][8][remarque]' => 'chocolat, pate',
                'sante_fiche[questions][0][remarque]' => 'Type 1',
            ]
        );

        $this->parent->submit($form);
        $crawler = $this->parent->followRedirect();

        $this->assertEquals(1, $crawler->filter('td:contains("chocolat")')->count());
        $this->assertEquals(5, $crawler->filter('td:contains("Non répondu")')->count());
    }

    public function testAccess()
    {
        $enfant = $this->getEnfant(['prenom' => 'Timeo']);

        $crawler = $this->parent->request('GET', '/parent/sante/show/'.$enfant->getUuid());
        $this->assertEquals(403, $this->parent->getResponse()->getStatusCode());

        $crawler = $this->parent->request('GET', '/parent/sante/edit/'.$enfant->getUuid());
        $this->assertEquals(403, $this->parent->getResponse()->getStatusCode());
    }

    public function testAdmin()
    {
        $enfant = $this->getEnfant(['prenom' => 'Marie']);

        $crawler = $this->admin->request('GET', '/admin/enfant/'.$enfant->getSlugname());

        $crawler = $this->admin->click($crawler->selectLink('Fiche santé')->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('td:contains("Ledoux")')->count());
    }

}
