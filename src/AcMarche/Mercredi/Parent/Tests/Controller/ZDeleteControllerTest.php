<?php

namespace AcMarche\Mercredi\Parent\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class ZDeleteControllerTest extends BaseUnit
{
    public function testDelete()
    {
        $crawler = $this->parent->request('GET', '/parent/enfants/');
        $crawler = $this->parent->click($crawler->selectLink('MICHEL Zora')->link());

        $dateParent = new \DateTime();
        $dateParent->modify('+8 week');
        $jourFr = $this->dateFilter($dateParent);

        $crawler = $this->parent->click($crawler->selectLink($jourFr)->link());
        $crawler = $this->parent->click($crawler->selectLink('Supprimer')->link());

        $this->parent->submit($crawler->selectButton('Supprimer')->form());

        $crawler = $this->parent->followRedirect();
        $this->assertEquals(0, $crawler->filter('td:contains("'.$jourFr.'")')->count());
    }
}
