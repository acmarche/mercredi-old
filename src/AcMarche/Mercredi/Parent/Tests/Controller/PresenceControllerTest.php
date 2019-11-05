<?php

namespace AcMarche\Mercredi\Parent\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class PresenceControllerTest extends BaseUnit
{
    public function testAddPresenceFichePasComplete()
    {
        $crawler = $this->parent->request('GET', '/parent/presences/select/enfant');

        $crawler = $this->parent->click($crawler->selectLink('MICHEL Lisa')->link());
        $crawler = $this->parent->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('div:contains("La fiche santé de votre enfant doit être complétée")')->count()
        );
    }

    public function testCompleteSante()
    {
        $this->completeSante('MICHEL Lisa');
    }

    public function testAddPresence()
    {
        $crawler = $this->parent->request('GET', '/parent/presences/select/enfant');

        $crawler = $this->parent->click($crawler->selectLink('MICHEL Lisa')->link());
        $dateParent = new \DateTime();
        $dateParent->modify('+1 week');
        $jourFr = $this->dateFilter($dateParent);

        $crawler = $this->parent->click($crawler->selectLink($jourFr)->link());
        //print_r($this->parent->getResponse()->getContent());
        $form = $crawler->selectButton('Confirmer sa présence')->form([]);

        $form['presence[remarques]'] = 'Mamy va le chercher';

        $this->parent->submit($form);
        $crawler = $this->parent->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("Mamy va le chercher")')->count());
    }

    public function testAddPresenceToDelete()
    {
        $this->completeSante('MICHEL Zora');
        $crawler = $this->parent->request('GET', '/parent/presences/select/enfant');

        $crawler = $this->parent->click($crawler->selectLink('MICHEL Zora')->link());

        $dateParent = new \DateTime();
        $dateParent->modify('+8 week');
        $jourFr = $this->dateFilter($dateParent);

        $crawler = $this->parent->click($crawler->selectLink($jourFr)->link());
        //print_r($this->parent->getResponse()->getContent());
        $form = $crawler->selectButton('Confirmer sa présence')->form([]);

        $form['presence[remarques]'] = 'Papy va le chercher';

        $this->parent->submit($form);
        $crawler = $this->parent->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("Papy va le chercher")')->count());
    }

    private function completeSante($nom)
    {
        $crawler = $this->parent->request('GET', '/parent/enfants/');
        $crawler = $this->parent->click($crawler->selectLink($nom)->link());
        $crawler = $this->parent->click($crawler->selectLink('Sa fiche santé')->link());
        $crawler = $this->parent->click($crawler->selectLink('Modifier')->link());

        $form = $crawler->selectButton('Enregistrer')->form(
            [
                'sante_fiche[medecinNom]' => 'Ledoux',
                'sante_fiche[medecinTelephone]' => '084 52 98 22',
                'sante_fiche[personneUrgence]' => 'Maman et papa',
                'sante_fiche[questions][0][reponse]' => 0,
                'sante_fiche[questions][1][reponse]' => 0,
                'sante_fiche[questions][2][reponse]' => 0,
                'sante_fiche[questions][3][reponse]' => 0,
                'sante_fiche[questions][4][reponse]' => 0,
                'sante_fiche[questions][5][reponse]' => 0,
                'sante_fiche[questions][6][reponse]' => 0,
                'sante_fiche[questions][7][reponse]' => 0,
                'sante_fiche[questions][8][reponse]' => 0,
            ]
        );

        $this->parent->submit($form);
        $crawler = $this->parent->followRedirect();

        $this->assertEquals(0, $crawler->filter('td:contains("Non répondu")')->count());
    }
}
