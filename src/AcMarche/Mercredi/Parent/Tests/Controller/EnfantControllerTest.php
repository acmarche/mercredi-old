<?php

namespace AcMarche\Mercredi\Parent\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class EnfantControllerTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->parent->request('GET', '/parent/enfants/');
        $crawler = $this->parent->click($crawler->selectLink('MICHEL Marie')->link());
        $crawler = $this->parent->click($crawler->selectLink('Modifier')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            [
                'enfant_edit[numero_national]' => '123456',
                'enfant_edit[accompagnateurs][0]' => 'Papy jf',
            ]
        );

        $this->parent->submit($form);
        $crawler = $this->parent->followRedirect();

        $this->assertEquals(1, $crawler->filter('html:contains("123456")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Papy jf")')->count());
    }

    public function testAccess()
    {
        $enfant = $this->getEnfant(['prenom' => 'Timeo']);

        $crawler = $this->parent->request('GET', '/parent/enfants/enfant/'.$enfant->getUuid());
        $this->assertEquals(403, $this->parent->getResponse()->getStatusCode());

        $crawler = $this->parent->request('GET', '/parent/enfants/edit/'.$enfant->getUuid());
        $this->assertEquals(403, $this->parent->getResponse()->getStatusCode());
    }
}
