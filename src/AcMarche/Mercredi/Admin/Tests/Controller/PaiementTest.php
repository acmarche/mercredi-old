<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

/**
 * Ajout d'un paiement depuis leurth timeo
 * 24-10-200 de 50.50 euros.
 */
class PaiementTest extends BaseUnit
{
    private $nom = 'Leruth';
    private $prenom = 'Timeo';
    private $montant = '50,50';
    private $date = '24/10/2015';
    private $type = 'Abonnement';

    private $nomT = 'sion';
    private $prenomT = 'natacha';
    private $lisaMontant = '51,32';
    private $lisaMontantTxt = '51.32';
    private $lisaDate = '24/10/2016';
    private $lisaType = 'Abonnement';
    private $lisaNom = 'MICHEL';
    private $lisaPrenom = 'Lisa';

    public function testPage()
    {
        $crawler = $this->admin->request('GET', '/admin/paiement/');
        $this->assertEquals(302, $this->admin->getResponse()->getStatusCode());
    }

    public function testAdd()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/'.$this->nom.'_'.$this->prenom);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Ajouter un paiement')->link());

        $form = $crawler->selectButton('Ajouter')->form([
            'paiement[montant]' => $this->montant,
            'paiement[date_paiement]' => $this->date,
            'paiement[type_paiement]' => $this->type,
        ]);

        $option = $crawler->filter('#paiement_enfant option:contains("'.strtoupper($this->nom).' '.$this->prenom.'")');
        $this->assertEquals(1, count($option));
        $value = $option->attr('value');
        $form['paiement[enfant]']->select($value);

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("'.strtoupper($this->nom).'")')->count());
    }

    public function testAddNat()
    {
        $crawler = $this->admin->request('GET', '/admin/tuteur/'.$this->nomT.'_'.$this->prenomT);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Ajouter un paiement')->link());

        $form = $crawler->selectButton('Ajouter')->form([
            'paiement[montant]' => $this->lisaMontant,
            'paiement[date_paiement]' => $this->lisaDate,
            'paiement[type_paiement]' => $this->lisaType,
        ]);

        $option = $crawler->filter('#paiement_enfant option:contains('.strtoupper($this->lisaNom).' '.$this->lisaPrenom.')');
        $this->assertEquals(1, count($option));
        $value = $option->attr('value');
        $form['paiement[enfant]']->select($value);

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("'.$this->lisaMontantTxt.' €")')->count());
    }
}
