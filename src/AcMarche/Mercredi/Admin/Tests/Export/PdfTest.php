<?php

namespace AcMarche\Mercredi\Admin\Tests\Export;

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
    public function tes2tPage()
    {
        $crawler = $this->admin->request('GET', '/admin/tuteur/michel_philippe');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Année 2016')->first()->link());
        //  print_r($this->admin->getResponse()->getContent());
        $this->assertTrue($this->admin->getResponse()->headers->contains('Content-Type', 'application/pdf'));
    }
}
