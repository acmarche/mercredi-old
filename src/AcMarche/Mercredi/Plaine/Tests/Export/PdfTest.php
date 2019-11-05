<?php

namespace AcMarche\Mercredi\Plaine\Tests\Export;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class PdfTest extends BaseUnit
{
    public function testPage()
    {
        $this->anonyme->request('GET', '/');
        $this->assertEquals(200, $this->anonyme->getResponse()->getStatusCode());
    }

    /**
     * @todo reactivate problem ssl
     */
    public function te22stPage()
    {
        $crawler = $this->admin->request('GET', '/plaine/plaine/carnaval_2020');
        $this->assertEquals(
            200,
            $this->admin->getResponse()->getStatusCode(),
            'Unexpected HTTP status code for GET /plaine/plaine/carnaval_2020'
        );

        $crawler = $this->admin->click($crawler->selectLink('Exporter en pdf')->link());
        //print_r($this->admin->getResponse()->getContent());
        $this->assertTrue($this->admin->getResponse()->headers->contains('Content-Type', 'application/pdf'));
    }
}
