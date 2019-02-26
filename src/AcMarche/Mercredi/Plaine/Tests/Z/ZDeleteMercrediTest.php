<?php

namespace AcMarche\Mercredi\Plaine\Tests\Z;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * je supprime un enfant puis son parent lorenzo dany
 * je supprime un parent puis son enfant nat timeo
 * Je supprime la date 01-10-2020
 */
class ZDeleteMercrediTest extends BaseUnit
{
    

    public function testDeleteLorenzo()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/leruth_lorenzo');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Supprimer l\'enfant')->link());

        $this->admin->submit($crawler->selectButton('Supprimer')->form());
        // print_r($this->admin->getResponse()->getContent());
        $crawler = $this->admin->followRedirect();

        $this->assertEquals(0, $crawler->filter('td:contains("Lorenzo")')->count());
    }

    public function testDeleteDany()
    {
        $crawler = $this->admin->request('GET', '/admin/tuteur/collard_dany');
        $crawler = $this->admin->click($crawler->selectLink('Supprimer le parent')->link());

        $this->admin->submit($crawler->selectButton('Supprimer')->form());
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("Le parent a bien été supprimé")')->count());
    }

    public function testDeleteNat()
    {
        $crawler = $this->admin->request('GET', '/admin/tuteur/leruth_nat');
        $crawler = $this->admin->click($crawler->selectLink('Supprimer le parent')->link());
        $this->admin->submit($crawler->selectButton('Supprimer')->form());

        $crawler = $this->admin->followRedirect();

        $this->assertNotRegExp('/LERUTH/', "Leruth nat pas supprime");
    }

    public function testDeleteJour()
    {
        $crawler = $this->admin->request('GET', '/admin/jour/');

        $crawler = $this->admin->click($crawler->selectLink('05-10-2016')->link());
        $this->assertGreaterThan(
            0,
            $crawler->filter('h3:contains("05-10-2016")')->count(),
            'Missing element h3:contains("05-10-2016")'
        );

        $crawler = $this->admin->click($crawler->selectLink('Supprimer')->link());
        $this->admin->submit($crawler->selectButton('Supprimer')->form());

        $crawler = $this->admin->followRedirect();

        $this->assertEquals(
            0,
            $crawler->filter('td:contains("05-10-2016")')->count(),
            'Missing element td:contains("05-10-2016")'
        );
    }
}
