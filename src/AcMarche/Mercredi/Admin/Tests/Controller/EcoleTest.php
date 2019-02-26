<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
 * Ajouter
 * Editer
 * Supprimer
 */

class EcoleTest extends BaseUnit
{
    private $nomBad = "Ecole de Spingfield";
    private $nom = "Ecole de Springfield";

    public function testindex()
    {
        $crawler = $this->admin->request('GET', '/admin/ecole/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
    }

    /**
     * Test page new
     * Test edit
     */
    public function testAdd()
    {
        $crawler = $this->admin->request('GET', '/admin/ecole/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Ajouter')->form(
            array(
                'ecole[nom]' => $this->nomBad,
            )
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('td:contains("'.$this->nomBad.'")')->count());
    }

    public function testEdit()
    {
        $crawler = $this->admin->request('GET', '/admin/ecole/');

        $crawler = $this->admin->click($crawler->selectLink($this->nomBad)->link());
        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            array(
                'ecole[nom]' => $this->nom,
            )
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("'.$this->nom.'")')->count());
    }

    public function T2estDelete()
    {
        $crawler = $this->admin->request('GET', '/admin/ecole/'.$this->nom);
        $crawler = $this->admin->click($crawler->selectLink('Supprimer')->link());

        $this->admin->submit($crawler->selectButton('Supprimer')->form());

        $crawler = $this->admin->followRedirect();

        $this->assertNotRegExp('/'.$this->nom.'/', $this->admin->getResponse()->getContent());
    }
}
