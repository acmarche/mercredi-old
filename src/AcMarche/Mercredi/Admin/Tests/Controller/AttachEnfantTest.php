<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Je vais sur la fiche dany
 * et j'ajoute un enfant lorenzo
 */
class AttachEnfantTest extends BaseUnit
{
    private $nom = "Collard";
    private $prenom = "Dany";

    private $nomEnfant = "Leruth";
    private $prenomEnfant = "Lorenzo";
    private $sexeEnfant = "Masculin";
    private $anneescolaire = "3M";

    public function testAttach()
    {
        $crawler = $this->admin->request('GET', '/admin/tuteur/'.$this->nom."_".$this->prenom);
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("'.strtoupper($this->nom).'")')->count());

        $crawler = $this->admin->click($crawler->selectLink('ajouter un nouveau')->link());
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Nouvel enfant")')->count());

        $form = $crawler->selectButton('Ajouter')->form(
            array(
                'enfant[nom]' => $this->nomEnfant,
                'enfant[prenom]' => $this->prenomEnfant,
                'enfant[sexe]' => $this->sexeEnfant,
                'enfant[annee_scolaire]' => $this->anneescolaire,
            )
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("'.$this->prenomEnfant.'")')->count());
        $this->assertGreaterThan(0, $crawler->filter('h4:contains("'.strtoupper($this->nom).'")')->count());
    }

    public function testExistant()
    {
        $crawler = $this->admin->request('GET', '/admin/tuteur/pennino_adrian');

        $enfant = $this->getEnfant(['nom' => 'Pennino']);
        $form = $crawler->selectButton('Définir comme enfant')->form(
            array(
                'tuteur_set_enfant[enfant]' => $enfant->getId(),
            )
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("Paulie")')->count());
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("PENNINO")')->count());
    }
}
