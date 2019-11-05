<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

/**
 * Je vais sur l'affiche leruth timeo
 * et j'ajoute le parent nat leruth.
 */
class AttachParentTest extends BaseUnit
{
    private $nomEnfant = 'leruth';
    private $prenomEnfant = 'timeo';

    private $nom = 'Leruth';
    private $prenom = 'Nat';
    private $sexe = 'Féminin';

    public function testAttach()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/'.$this->nomEnfant.'_'.$this->prenomEnfant);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('ajouter un nouveau')->link());
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Nouveau parent")')->count());

        $form = $crawler->selectButton('Ajouter')->form(
            [
                'tuteur[nom]' => $this->nom,
                'tuteur[prenom]' => $this->prenom,
                'tuteur[sexe]' => $this->sexe,
                'tuteur[telephone]' => '081',
            ]
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("'.strtoupper($this->nom).'")')->count());
    }

    public function testAttachExistant()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/balboa_rocky');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $tuteur = $this->getTuteur(['nom' => 'Pennino']);
        $form = $crawler->selectButton('Définir comme parent')->form(
            [
                'enfant_set_tuteur[tuteur]' => $tuteur->getId(),
            ]
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('h4:contains("PENNINO")')->count());
    }
}
