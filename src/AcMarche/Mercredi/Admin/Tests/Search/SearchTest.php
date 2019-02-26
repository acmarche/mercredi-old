<?php

namespace AcMarche\Mercredi\Admin\Tests\Search;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class SearchTest extends BaseUnit
{
    public function testEnfant()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Rechercher')->form(array(
            'search_enfant[nom]' => 'Leruth',
        ));

        $ecole_option = $crawler->filter('#search_enfant_ecole option:contains("Hargimont communal")');
        $this->assertGreaterThan(0, count($ecole_option), 'Hargimont communal non trouvé');
        $ecole = $ecole_option->attr('value');
        $form['search_enfant[ecole]']->select($ecole);

        $annee_option = $crawler->filter('#search_enfant_annee_scolaire option:contains("3M")');
        $this->assertGreaterThan(0, count($annee_option), '3M non trouvé');
        $annee = $annee_option->attr('value');
        $form['search_enfant[annee_scolaire]']->select($annee);

        $crawler = $this->admin->submit($form);

        //print_r($this->admin->getResponse()->getContent());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Timeo")')->count());
    }

    /**
     *
     */
    public function testParent()
    {
        $crawler = $this->admin->request('GET', '/admin/tuteur/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Rechercher')->form(array(
            'search_tuteur[nom]' => 'Collard',
        ));

        $crawler =  $this->admin->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('td:contains("COLLARD Dany")')->count());
    }

    public function testPresence()
    {
        $crawler = $this->admin->request('GET', '/admin/presence/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Rechercher')->form(array());

        $jour_option = $crawler->filter('#search_presence_jour option:contains("05-10-2015 Lundi")');
        $this->assertGreaterThan(0, count($jour_option), '05-10-2015 Lundi non trouvé');
        $jour = $jour_option->attr('value');
        $form['search_presence[jour]']->select($jour);

        $ecole_option = $crawler->filter('#search_presence_ecole option:contains("Hargimont communal")');
        $this->assertGreaterThan(0, count($ecole_option), 'Hargimont communal non trouvé');
        $ecole = $ecole_option->attr('value');
        $form['search_presence[ecole]']->select($ecole);

        $crawler = $this->admin->submit($form);
        // print_r($this->admin->getResponse()->getContent());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Timeo")')->count());
    }

    public function testPresenceByMois()
    {
        $crawler = $this->admin->request('GET', '/admin/presence/by/mois');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Rechercher')->form(array(
            'search_presence_by_month[mois]' => '10/2015'
        ));

        $quoi_option = $crawler->filter('#search_presence_by_month_quoi option:contains("Mercredi et Plaines")');
        $this->assertGreaterThan(0, count($quoi_option), 'Mercredi et Plaines non trouvé');
        $quoi = $quoi_option->attr('value');
        $form['search_presence_by_month[quoi]']->select($quoi);

        $crawler = $this->admin->submit($form);
        $this->assertGreaterThan(0, $crawler->filter('td:contains("LERUTH Timeo")')->count());
    }
}
