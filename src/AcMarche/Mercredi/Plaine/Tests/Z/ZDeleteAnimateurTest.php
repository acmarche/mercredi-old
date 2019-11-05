<?php

namespace AcMarche\Mercredi\Plaine\Tests\Z;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

/**
 * je supprime un enfant puis son parent lorenzo dany
 * je supprime un parent puis son enfant nat timeo
 * Je supprime la date 01-10-2020.
 */
class ZDeleteAnimateurTest extends BaseUnit
{
    public function testDeleteCarine()
    {
        $crawler = $this->admin->request('GET', '/admin/animateur/senechal_carine');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode(), 'Unexpected HTTP status code for GET /admin/animateur/senechal_carine');

        $crawler = $this->admin->click($crawler->selectLink('Supprimer')->link());

        $this->admin->submit($crawler->selectButton('Supprimer')->form());

        $crawler = $this->admin->followRedirect();

        $this->assertNotRegExp('/Senechal/', 'Carine senechal pas supprime');
    }
}
