<?php

namespace AcMarche\Mercredi\Admin\Tests\Export;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class XlsTest extends BaseUnit
{
    public function testExportByMois()
    {
        $crawler = $this->admin->request('GET', '/admin/presence/by/mois');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Rechercher')->form(
            array(
                'search_presence_by_month[mois]' => '10/2016',
            )
        );

        $crawler = $this->admin->submit($form);
        $this->admin->click($crawler->selectLink('Par défaut')->link());

        $this->assertTrue(
            $this->admin->getResponse()->headers->contains(
                'Content-Type',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            )
        );

        $this->admin->click($crawler->selectLink('Pour l\'One')->link());

        $this->assertTrue(
            $this->admin->getResponse()->headers->contains(
                'Content-Type',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            )
        );
    }
}
