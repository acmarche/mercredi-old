<?php

namespace AcMarche\Mercredi\Plaine\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PlainePresenceControllerTest extends BaseUnit
{
    private $urlCarnaval = "carnaval_2020";
    private $nom = "LERUTH Timeo";
    private $dateVerif = "11-10-2020 Dimanche";
    private $dateVerif2 = "10-10-2020 Samedi";

    /**
     * La date 11-10 a ete ajoute alors que timeo
     * deja inscrit, il doit l'etre doffice pour cette nouvelle date
     * Je selectionne l'enfant
     *
     */
    public function testChangeDateEnfant()
    {
        //je vais sur carnaval
        $crawler = $this->admin->request('GET', '/plaine/plaine/' . $this->urlCarnaval);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        //je vais sur dineur
        $crawler = $this->admin->click($crawler->selectLink($this->nom)->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        //je vais sur date de presence
        $this->assertGreaterThan(0, $crawler->filter('td:contains("' . $this->dateVerif . '")')->count());

        //je vais lui remettre la date
        $crawler = $this->admin->click($crawler->selectLink("Modifiers les présences")->link());

        $form = $crawler->selectButton("Mettre à jour")->form(array());

        $form['plaine_presence_jours[jours][0]']->tick();

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("' . $this->dateVerif2 . '")')->count());
    }
}
