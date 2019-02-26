<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 *
 * Test de la page principale
 */
class MessageTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->admin->request('GET', '/admin/message/');

        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Envoie d\'un message aux parents")')->count());
    }

    public function testSend()
    {
        $crawler = $this->admin->request('GET', '/admin/message/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Ecrire le message')->link());
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Nouveau message")')->count());

        $form = $crawler->selectButton('Envoyer le message')->form(
            array(
                'message[sujet]' => 'Test de message',
                'message[texte]' => 'Test de message',
            )
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('div:contains("Le message a bien été envoyé")')->count());
    }

    public function testFromTuteur()
    {
        $crawler = $this->admin->request('GET', '/admin/tuteur/michel_philippe');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Envoyer un message')->link());

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Nouveau message")')->count());

        $form = $crawler->selectButton('Envoyer le message')->form(
            array(
                'message[sujet]' => 'Test de message',
                'message[texte]' => 'Test de message',
            )
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('div:contains("Le message a bien été envoyé")')->count());
    }

    public function testFromGroup()
    {
        $crawler = $this->admin->request('GET', '/admin/presence/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Rechercher')->form(array());

        $jour_option = $crawler->filter('#search_presence_jour option:contains("11-11-2016 Vendredi")');
        $this->assertGreaterThan(0, count($jour_option), '11-11-2016 non trouvé');
        $jour = $jour_option->attr('value');
        $form['search_presence[jour]']->select($jour);
        $this->admin->submit($form);

        $crawler = $this->admin->request('GET', '/admin/message/new/groupescolaire/petits');
        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Nouveau message")')->count());

        $form = $crawler->selectButton('Envoyer le message')->form(
            array(
                'message[sujet]' => 'Test de message',
                'message[texte]' => 'Test de message',
            )
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('div:contains("Le message a bien été envoyé")')->count());
    }
}
